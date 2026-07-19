<?php
declare(strict_types=1);

/**
 * Return the role that must be assigned at registration time.
 * Exact, normalized email matches only; every other signup receives $defaultRole.
 */
function beyond_signup_role(string $email, string $defaultRole = 'user'): string
{
    $normalized = strtolower(trim($email));

    return match ($normalized) {
        'rosiergreg@gmail.com' => 'super_admin',
        'admin@beyondimagination.co.technology' => 'admin',
        default => $defaultRole,
    };
}
