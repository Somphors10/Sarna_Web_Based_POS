<?php

/**
 * Score password strength from length and character variety (informational only).
 */
function password_strength_score(string $password): int
{
    $score = 0;
    $length = strlen($password);

    if ($length >= 8) {
        $score++;
    }
    if ($length >= 12) {
        $score++;
    }
    if ($length >= 16) {
        $score++;
    }
    if (preg_match('/[a-zA-Z]/', $password)) {
        $score++;
    }
    if (preg_match('/[A-Z]/', $password)) {
        $score++;
    }
    if (preg_match('/\d/', $password)) {
        $score++;
    }
    if (preg_match('/[^A-Za-z0-9]/', $password)) {
        $score++;
    }

    return $score;
}

function is_common_weak_password(string $password): bool
{
    static $weak = [
        'password',
        '12345678',
        '123456789',
        '1234567890',
        'qwerty123',
        'admin123',
        'pointofsale',
        'password1',
        'letmein',
        'welcome1',
    ];

    return in_array(strtolower($password), $weak, true);
}

function is_strong_password(?string $password): bool
{
    if ($password === null || $password === '') {
        return false;
    }

    if (strlen($password) < 8) {
        return false;
    }

    if (!preg_match('/[a-zA-Z]/', $password)) {
        return false;
    }

    if (!preg_match('/\d/', $password)) {
        return false;
    }

    if (preg_match('/^(.)\1+$/', $password)) {
        return false;
    }

    if (is_common_weak_password($password)) {
        return false;
    }

    return true;
}

/**
 * @deprecated Use server-side is_strong_password() only; HTML pattern removed from forms.
 */
function strong_password_pattern(): string
{
    return '/.+/';
}

/**
 * @deprecated HTML pattern validation removed — strength is checked on the server.
 */
function strong_password_js_pattern(): string
{
    return '';
}
