<?php

namespace App\Libraries;

use CodeIgniter\Email\Email;
use Config\Services;
use Throwable;

/**
 * Send Super Admin → owner KHQR payment email after activation.
 */
class PlatformMail
{
    /**
     * @return array{ok:bool, error:string}
     */
    public function sendKhqrPayment(object $request, string $pay_url, float $price): array
    {
        $to = trim((string)($request->owner_email ?? ''));
        if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
            return ['ok' => false, 'error' => 'Owner email is missing or invalid.'];
        }

        $qr_path = FCPATH . 'images/payment/aba-khqr-code.png';
        $qr_ready = is_file($qr_path);
        $html = view('saas/email_khqr', [
            'request' => $request,
            'pay_url' => $pay_url,
            'price' => $price,
            'qr_cid' => $qr_ready,
        ]);

        $this->writeOutbox((int)($request->request_id ?? 0), $to, $html, $qr_path);

        return $this->deliver(
            $to,
            'Pay to activate ' . (string)$request->company_name,
            $html,
            $qr_ready ? $qr_path : null,
            true
        );
    }

    /**
     * @return array{ok:bool, error:string}
     */
    public function sendVerifyEmail(object $request, string $verify_url): array
    {
        $to = trim((string)($request->owner_email ?? ''));
        if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
            return ['ok' => false, 'error' => 'Owner email is missing or invalid.'];
        }

        $html = view('saas/email_verify', [
            'request' => $request,
            'verify_url' => $verify_url,
        ]);
        $this->writeOutbox((int)($request->request_id ?? 0), $to, $html, '');

        return $this->deliver($to, 'Verify your email for ' . (string)$request->company_name, $html);
    }

    /**
     * @return array{ok:bool, error:string}
     */
    public function sendPasswordReset(object $employee, string $company_name, string $reset_url): array
    {
        $to = strtolower(trim((string)($employee->email ?? '')));
        if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
            return ['ok' => false, 'error' => 'Account email is missing or invalid.'];
        }

        $html = view('login/email_reset_password', [
            'employee' => $employee,
            'company_name' => $company_name,
            'reset_url' => $reset_url,
        ]);

        $this->writeOutbox((int)($employee->person_id ?? 0), $to, $html, '');

        return $this->deliver($to, 'Reset your WBPOS password', $html);
    }

    /**
     * Subscription expiry / renew reminder to shop owner Gmail.
     *
     * @param array{
     *   to:string,
     *   company_name:string,
     *   owner_name:string,
     *   expires_label:string,
     *   stage:string,
     *   pay_url:string,
     *   days_left:int,
     *   tenant_id?:int
     * } $payload
     * @return array{ok:bool, error:string}
     */
    public function sendExpiryReminder(array $payload): array
    {
        $to = strtolower(trim((string)($payload['to'] ?? '')));
        if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
            return ['ok' => false, 'error' => 'Owner email is missing or invalid.'];
        }

        $stage = (string)($payload['stage'] ?? '7');
        $company = (string)($payload['company_name'] ?? 'Your shop');
        $html = view('saas/email_expiry', [
            'company_name' => $company,
            'owner_name' => (string)($payload['owner_name'] ?? ''),
            'expires_label' => (string)($payload['expires_label'] ?? ''),
            'stage' => $stage,
            'pay_url' => (string)($payload['pay_url'] ?? site_url('saas/checkout')),
            'days_left' => (int)($payload['days_left'] ?? 0),
        ]);

        $this->writeOutbox((int)($payload['tenant_id'] ?? 0), $to, $html, '');

        $subject = match ($stage) {
            'expired' => 'Action needed: ' . $company . ' subscription expired',
            '1' => 'Reminder: ' . $company . ' subscription ends tomorrow',
            '3' => 'Reminder: ' . $company . ' subscription ends in a few days',
            default => 'Reminder: ' . $company . ' subscription ends in 7 days',
        };

        return $this->deliver($to, $subject, $html);
    }

    /**
     * @return array{ok:bool, error:string}
     */
    public function sendTest(string $to): array
    {
        $to = strtolower(trim($to));
        if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
            return ['ok' => false, 'error' => 'Enter a valid test email address.'];
        }

        $html = '<p>WBPOS can send mail. Owner verification links will use this Gmail.</p>';

        return $this->deliver($to, 'WBPOS test email', $html);
    }

    /**
     * @return array{ok:bool, error:string}
     */
    private function deliver(string $to, string $subject, string $html, ?string $attach = null, bool $inline = false): array
    {
        $smtp = $this->resolvedSmtp();
        if (!in_array($smtp['mode'], ['gmail', 'smtp', 'mailhog'], true)) {
            return ['ok' => false, 'error' => self::notConfiguredMessage()];
        }

        $attempts = [$smtp];
        if ($smtp['mode'] === 'gmail' && (int)$smtp['port'] === 587) {
            $attempts[] = array_merge($smtp, ['port' => 465, 'crypto' => 'ssl']);
        }

        $last_error = '';
        foreach ($attempts as $cfg) {
            try {
                $email = $this->makeMailer($cfg);
                $email->setTo($to);
                $email->setSubject($subject);
                $body = $html;
                if ($attach !== null && is_file($attach)) {
                    $email->attach($attach, $inline ? 'inline' : 'attachment', 'wbpos-khqr.png');
                    if ($inline) {
                        $cid = $email->setAttachmentCID($attach);
                        if (is_string($cid) && $cid !== '') {
                            $body = str_replace('cid:wbpos-khqr', 'cid:' . $cid, $body);
                        }
                    }
                }
                $email->setMessage($body);
                if ($email->send()) {
                    return ['ok' => true, 'error' => ''];
                }
                $last_error = 'SMTP ' . $cfg['host'] . ':' . $cfg['port']
                    . ' crypto=' . ($cfg['crypto'] === '' ? 'none' : $cfg['crypto'])
                    . '. ' . strip_tags($email->printDebugger(['headers']));
            } catch (Throwable $e) {
                $last_error = $e->getMessage();
                log_message('error', 'Platform mail failed: ' . $last_error);
            }
        }

        return ['ok' => false, 'error' => $last_error];
    }

    /**
     * @return array{mode:string, host:string, port:int, crypto:string, user:string, from:string}
     */
    public static function deliveryInfo(): array
    {
        $smtp = (new self())->resolvedSmtp();

        return [
            'mode' => $smtp['mode'],
            'host' => $smtp['host'],
            'port' => $smtp['port'],
            'crypto' => $smtp['crypto'],
            'user' => $smtp['user'],
            'from' => $smtp['from'],
        ];
    }

    /**
     * @param array{host:string, port:int, crypto:string, user:string, pass:string, from:string, from_name:string} $smtp
     */
    private function makeMailer(array $smtp): Email
    {
        $email = new Email();
        $email->initialize([
            'mailType' => 'html',
            'charset' => 'UTF-8',
            'validate' => false,
            'protocol' => 'smtp',
            'SMTPHost' => $smtp['host'],
            'SMTPUser' => $smtp['user'],
            'SMTPPass' => $smtp['pass'],
            'SMTPPort' => $smtp['port'],
            'SMTPTimeout' => 25,
            'SMTPCrypto' => $smtp['crypto'],
            'newline' => "\r\n",
            'CRLF' => "\r\n",
        ]);
        $email->setFrom($smtp['from'], $smtp['from_name']);

        return $email;
    }

    /**
     * DirectAdmin / shared hosting: send through the server (no Super Admin Gmail step).
     *
     * @param array{from:string, from_name:string} $smtp
     * @return array{ok:bool, error:string}
     */
    private function deliverViaPhpMail(array $smtp, string $to, string $subject, string $html): array
    {
        $last_error = '';
        foreach (['mail', 'sendmail'] as $protocol) {
            try {
                $email = new Email();
                $email->initialize([
                    'mailType' => 'html',
                    'charset' => 'UTF-8',
                    'validate' => false,
                    'protocol' => $protocol,
                    'mailPath' => '/usr/sbin/sendmail',
                    'newline' => "\r\n",
                    'CRLF' => "\r\n",
                    'wordWrap' => false,
                ]);
                $email->setFrom($smtp['from'], $smtp['from_name']);
                $email->setTo($to);
                $email->setSubject($subject);
                $email->setMessage($html);
                if ($email->send()) {
                    return ['ok' => true, 'error' => ''];
                }
                $last_error = strip_tags($email->printDebugger(['headers']));
            } catch (Throwable $e) {
                $last_error = $e->getMessage();
                log_message('error', 'Server mail failed (' . $protocol . '): ' . $last_error);
            }
        }

        return ['ok' => false, 'error' => $last_error !== '' ? $last_error : 'The hosting server could not send mail.'];
    }

    private function defaultFromAddress(string $fallback): string
    {
        $from = trim((string)env('email.fromEmail'));
        if ($from !== '' && filter_var($from, FILTER_VALIDATE_EMAIL)) {
            return $from;
        }

        $host = strtolower((string)(service('request')->getServer('HTTP_HOST') ?? ''));
        $host = (string)preg_replace('/:\d+$/', '', $host);
        if ($host !== '' && !in_array($host, ['localhost', '127.0.0.1', '::1'], true)) {
            return 'noreply@' . $host;
        }

        return $fallback !== '' ? $fallback : 'noreply@localhost';
    }

    /**
     * Gmail App Password (saved by Super Admin) wins. MailHog is local-only and must not use TLS/SSL.
     *
     * @return array{mode:string, host:string, port:int, crypto:string, user:string, pass:string, from:string, from_name:string}
     */
    private function resolvedSmtp(): array
    {
        $gmail = $this->gmailFromMeta();
        if ($gmail !== null) {
            return $gmail;
        }

        $host = strtolower(trim((string)env('email.SMTPHost')));
        $port = (int)env('email.SMTPPort');
        $from_name = trim((string)env('email.fromName')) ?: 'WBPOS';
        $from = $this->defaultFromAddress('wbpos@localhost');
        $smtp_user = trim((string)env('email.SMTPUser'));
        $smtp_pass = (string)env('email.SMTPPass');
        $crypto = strtolower(trim((string)env('email.SMTPCrypto')));
        $local = self::isLocalDevHost();
        $is_mailhog = $host === 'mailhog' || $port === 1025;

        if ($is_mailhog && $local) {
            return [
                'mode' => 'mailhog',
                'host' => $host !== '' ? $host : 'mailhog',
                'port' => $port > 0 ? $port : 1025,
                'crypto' => '',
                'user' => '',
                'pass' => '',
                'from' => $from,
                'from_name' => $from_name,
            ];
        }

        if ($smtp_user !== '' && $smtp_pass !== '') {
            $smtp_host = $host;
            if ($smtp_host === '' || $smtp_host === 'mailhog') {
                $smtp_host = str_ends_with(strtolower($smtp_user), '@gmail.com')
                    ? 'smtp.gmail.com'
                    : 'localhost';
            }

            return [
                'mode' => 'smtp',
                'host' => $smtp_host,
                'port' => $port > 0 ? $port : 587,
                'crypto' => $crypto !== '' ? $crypto : 'tls',
                'user' => $smtp_user,
                'pass' => $smtp_pass,
                'from' => filter_var($smtp_user, FILTER_VALIDATE_EMAIL) ? $smtp_user : $from,
                'from_name' => $from_name,
            ];
        }

        if ($host !== '' && !$is_mailhog) {
            return [
                'mode' => 'smtp',
                'host' => $host,
                'port' => $port > 0 ? $port : 587,
                'crypto' => $crypto,
                'user' => $smtp_user,
                'pass' => $smtp_pass,
                'from' => $from,
                'from_name' => $from_name,
            ];
        }

        return [
            'mode' => 'none',
            'host' => '',
            'port' => 0,
            'crypto' => '',
            'user' => '',
            'pass' => '',
            'from' => $from,
            'from_name' => $from_name,
        ];
    }

    /**
     * @return array{mode:string, host:string, port:int, crypto:string, user:string, pass:string, from:string, from_name:string}|null
     */
    private function gmailFromMeta(): ?array
    {
        $meta = (new PlatformArchitecture())->getTemplateMeta();
        $user = trim((string)($meta['email_smtp_user'] ?? ''));
        $pass = $this->decryptPass((string)($meta['email_smtp_pass'] ?? ''));
        if ($user === '' || $pass === '' || !filter_var($user, FILTER_VALIDATE_EMAIL)) {
            return null;
        }

        return [
            'mode' => 'gmail',
            'host' => 'smtp.gmail.com',
            'port' => 587,
            'crypto' => 'tls',
            'user' => $user,
            'pass' => $pass,
            'from' => $user,
            'from_name' => trim((string)env('email.fromName')) ?: 'WBPOS',
        ];
    }

    private function decryptPass(string $value): string
    {
        $value = trim($value);
        if ($value === '') {
            return '';
        }

        if (function_exists('check_encryption') && check_encryption()) {
            try {
                return Services::encrypter()->decrypt($value);
            } catch (Throwable $e) {
                return $value;
            }
        }

        return $value;
    }

    public static function encryptPass(string $value): string
    {
        $value = trim($value);
        if ($value === '') {
            return '';
        }

        if (function_exists('check_encryption') && check_encryption()) {
            try {
                return Services::encrypter()->encrypt($value);
            } catch (Throwable $e) {
                return $value;
            }
        }

        return $value;
    }

    public static function outboxPath(int $request_id): string
    {
        return WRITEPATH . 'mail_outbox/request_' . $request_id . '.html';
    }

    public static function hasOutbox(int $request_id): bool
    {
        return is_file(self::outboxPath($request_id));
    }

    public static function isLocalDevHost(): bool
    {
        $host = strtolower((string)(service('request')->getServer('HTTP_HOST') ?? ''));
        $host = (string)preg_replace('/:\d+$/', '', $host);

        return in_array($host, ['localhost', '127.0.0.1', '::1'], true);
    }

    public static function notConfiguredMessage(): string
    {
        return 'Gmail did not receive this. Super Admin must save a Gmail App Password once on Email settings. After that, register/resend emails the owner automatically.';
    }

    public static function isReady(): bool
    {
        $mode = self::deliveryInfo()['mode'] ?? '';

        return in_array($mode, ['gmail', 'smtp'], true);
    }

    private function writeOutbox(int $request_id, string $to, string $html, string $qr_path): void
    {
        $dir = WRITEPATH . 'mail_outbox';
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }

        $preview = $html;
        if (is_file($qr_path)) {
            $preview = str_replace(
                'cid:wbpos-khqr',
                'data:image/png;base64,' . base64_encode((string)file_get_contents($qr_path)),
                $preview
            );
        }

        @file_put_contents(
            $dir . '/request_' . $request_id . '.html',
            "<!-- to: {$to} -->\n" . $preview
        );
    }
}
