<?php

/**
 * Strong password: min 8 chars, upper, lower, digit, special character.
 */
function strong_password_pattern(): string
{
    return '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/';
}

function is_strong_password(?string $password): bool
{
    if ($password === null || $password === '') {
        return false;
    }

    return (bool) preg_match(strong_password_pattern(), $password);
}

function strong_password_js_pattern(): string
{
    return '^(?=.*[a-z])(?=.*[A-Z])(?=.*\\d)(?=.*[^A-Za-z0-9]).{8,}$';
}
