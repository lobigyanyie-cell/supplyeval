<?php

namespace App\Config;

/**
 * Validity window for rows in password_resets (forgot password + team invites).
 */
final class PasswordReset
{
    public const TOKEN_TTL_HOURS = 48;
}
