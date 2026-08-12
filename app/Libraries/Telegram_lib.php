<?php

namespace App\Libraries;

/**
 * Send platform alerts to Super Admin via Telegram Bot API.
 */
class Telegram_lib
{
    private ?string $bot_token;
    private ?string $chat_id;

    public function __construct()
    {
        $this->bot_token = env('telegram.bot_token') ?: null;
        $this->chat_id = env('telegram.super_admin_chat_id') ?: null;
    }

    public function is_enabled(): bool
    {
        $enabled = env('telegram.enabled');

        if ($enabled !== null && $enabled !== '' && !filter_var($enabled, FILTER_VALIDATE_BOOLEAN)) {
            return false;
        }

        return !empty($this->bot_token) && !empty($this->chat_id);
    }

    /**
     * Notify Super Admin that a new company subscription request was submitted.
     *
     * @param array<string, mixed> $request
     */
    public function notify_new_subscription_request(array $request): bool
    {
        if (!$this->is_enabled()) {
            return false;
        }

        $owner_name = trim(($request['owner_first_name'] ?? '') . ' ' . ($request['owner_last_name'] ?? ''));
        $plan_label = (string)($request['plan_name'] ?? 'Subscription plan');
        $plan_price = isset($request['plan_price']) ? number_format((float)$request['plan_price'], 2) : null;
        $review_url = (string)($request['review_url'] ?? '');
        $company_name = (string)($request['company_name'] ?? '');
        $tenant_code = (string)($request['tenant_code'] ?? '');
        $username = (string)($request['owner_username'] ?? '');
        $email = (string)($request['owner_email'] ?? '');
        $phone = trim((string)($request['owner_phone'] ?? ''));
        $payment_ref = (string)($request['payment_reference'] ?? '');
        $request_id = !empty($request['request_id']) ? (int)$request['request_id'] : null;

        $lines = [
            $this->field_line('📋', 'New company registration', '', false, true),
            '',
            $this->field_line('🏢', 'Company', $company_name),
            $this->field_line('🔖', 'Code', $tenant_code, true),
            '',
            $this->field_line('👤', 'Owner', $owner_name),
            $this->field_line('🔑', 'Username', $username, true),
        ];

        if ($email !== '') {
            $lines[] = $this->field_line('✉️', 'Email', $email);
        }

        if ($phone !== '') {
            $lines[] = $this->field_line('📞', 'Phone', $phone);
        }

        $lines[] = '';

        if ($plan_price !== null) {
            $lines[] = $this->field_line('💳', 'Plan', $plan_label . ' ($' . $plan_price . '/month)');
        } else {
            $lines[] = $this->field_line('💳', 'Plan', $plan_label);
        }

        $lines[] = $this->field_line('🧾', 'Payment ref', $payment_ref, true);

        if ($request_id !== null) {
            $lines[] = $this->field_line('🆔', 'Request', '#' . $request_id);
        }

        $lines[] = '';
        $lines[] = $this->field_line('⏳', 'Status', 'Pending approval');

        $keyboard = null;
        if ($review_url !== '' && $this->is_telegram_button_url($review_url)) {
            $keyboard = [
                'inline_keyboard' => [
                    [
                        [
                            'text' => 'Review in Super Admin',
                            'url'  => $review_url,
                        ],
                    ],
                ],
            ];
        } elseif ($review_url !== '') {
            $lines[] = '';
            $lines[] = '<a href="' . $this->escape_attr($review_url) . '">Open Super Admin → Requests</a>';
        }

        return $this->send_message(implode("\n", $lines), $keyboard);
    }

    /**
     * One line: icon + bold label + value (e.g. "🏢 Company: kkk").
     */
    private function field_line(string $icon, string $label, string $value, bool $use_code = false, bool $title_only = false): string
    {
        if ($title_only) {
            return $icon . ' <b>' . $this->escape($label) . '</b>';
        }

        $formatted_value = $use_code
            ? '<code>' . $this->escape($value) . '</code>'
            : $this->escape($value);

        return $icon . ' <b>' . $this->escape($label) . ':</b> ' . $formatted_value;
    }

    /**
     * Telegram inline buttons require a public http(s) URL — localhost is rejected.
     */
    private function is_telegram_button_url(string $url): bool
    {
        $parts = parse_url($url);
        if ($parts === false || empty($parts['scheme']) || empty($parts['host'])) {
            return false;
        }

        if (!in_array(strtolower($parts['scheme']), ['http', 'https'], true)) {
            return false;
        }

        $host = strtolower($parts['host']);

        if (in_array($host, ['localhost', '127.0.0.1', '0.0.0.0', '::1'], true)) {
            return false;
        }

        if (str_ends_with($host, '.local') || str_ends_with($host, '.localhost')) {
            return false;
        }

        return true;
    }

    /**
     * @param array<string, mixed>|null $reply_markup
     */
    private function send_message(string $text, ?array $reply_markup = null): bool
    {
        if (!$this->is_enabled()) {
            return false;
        }

        $url = 'https://api.telegram.org/bot' . $this->bot_token . '/sendMessage';

        $payload = [
            'chat_id'                  => $this->chat_id,
            'text'                     => $text,
            'parse_mode'               => 'HTML',
            'disable_web_page_preview' => true,
        ];

        if ($reply_markup !== null) {
            $payload['reply_markup'] = $reply_markup;
        }

        $ch = curl_init($url);
        if ($ch === false) {
            log_message('error', 'Telegram: unable to initialize cURL.');

            return false;
        }

        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT        => 15,
        ]);

        $response = curl_exec($ch);
        $http_code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        if ($response === false || $http_code !== 200) {
            log_message(
                'error',
                'Telegram send failed. HTTP ' . $http_code . '. ' . ($curl_error ?: (string)$response)
            );

            return false;
        }

        $decoded = json_decode((string)$response, true);
        if (!is_array($decoded) || empty($decoded['ok'])) {
            log_message('error', 'Telegram API error: ' . (string)$response);

            return false;
        }

        return true;
    }

    private function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    private function escape_attr(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
