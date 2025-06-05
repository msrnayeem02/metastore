<?php


namespace App\Helpers;

class PasswordHelper
{
    public static function generateSecurePassword(): string
    {
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        $specialChars = '!@#$%^&*';

        // Initialize password array with required characters
        $password = [
            $uppercase[rand(0, strlen($uppercase) - 1)],
            $lowercase[rand(0, strlen($lowercase) - 1)],
            $numbers[rand(0, strlen($numbers) - 1)],
            $specialChars[rand(0, strlen($specialChars) - 1)]
        ];

        // Fill remaining length (total 8 characters as per policy)
        $remainingLength = 8 - count($password);
        $allChars = $uppercase . $lowercase . $numbers . $specialChars;

        for ($i = 0; $i < $remainingLength; $i++) {
            $password[] = $allChars[rand(0, strlen($allChars) - 1)];
        }

        // Shuffle to make the pattern less predictable
        shuffle($password);

        $finalPassword = implode('', $password);

        // Verify password meets all requirements
        if (
            !preg_match('/[A-Z]/', $finalPassword) ||
            !preg_match('/[a-z]/', $finalPassword) ||
            !preg_match('/[0-9]/', $finalPassword) ||
            !preg_match('/[' . preg_quote($specialChars) . ']/', $finalPassword) ||
            strlen($finalPassword) < 8
        ) {
            return self::generateSecurePassword();
        }

        return $finalPassword;
    }
}
