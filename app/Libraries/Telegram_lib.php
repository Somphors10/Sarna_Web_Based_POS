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

        $lines = [
            '<b>New company registration</b>',
            '',
            '<b>Company:</b> ' . $this->escape((string)($request['company_name'] ?? '')),
            '<b>Company code:</b> ' . $this->escape((string)($request['tenant_code'] ?? '')),
            '<b>Owner:</b> ' . $this->escape($owner_name),
            '<b>Username:</b> ' . $this->escape((string)($request['owner_username'] ?? '')),
            '<b>Email:</b> ' . $this->escape((string)($request['owner_email'] ?? '')),
        ];

        $phone = trim((string)($request['owner_phone'] ?? ''));
        if ($phone !== '') {
            $lines[] = '<b>Phone:</b> ' . $this->escape($phone);
        }

        if ($plan_price !== null) {
            $lines[] = '<b>Plan:</b> ' . $this->escape($plan_label) . ' ($' . $plan_price . '/month)';
        } else {
            $lines[] = '<b>Plan:</b> ' . $this->escape($plan_label);
        }

        $lines[] = '<b>Payment ref:</b> ' . $this->escape((string)($request['payment_reference'] ?? ''));

        if (!empty($request['request_id'])) {
            $lines[] = '<b>Request ID:</b> #' . (int)$request['request_id'];
        }

        $lines[] = '';
        $lines[] = 'Status: <b>Pending approval</b>';

        if ($review_url !== '') {
            $lines[] = '';
            $lines[] = '<a href="' . $this->escape_attr($review_url) . '">Open Super Admin → Requests</a>';
        }

        return $this->send_message(implode("\n", $lines));
    }

    private function send_message(string $text): bool
    {
        if (!$this->is_enabled()) {
            return false;
        }

        $url = 'https://api.telegram.org/bot' . $this->bot_token . '/sendMessage';

        $payload = http_build_query([
            'chat_id'                  => $this->chat_id,
            'text'                     => $text,
            'parse_mode'               => 'HTML',
            'disable_web_page_preview' => 'true',
        ]);

        $ch = curl_init($url);
        if ($ch === false) {
            log_message('error', 'Telegram: unable to initialize cURL.');

            return false;
        }

        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
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
