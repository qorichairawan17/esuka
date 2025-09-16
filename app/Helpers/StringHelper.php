<?php

namespace App\Helpers;

class StringHelper
{
    public static function splitName(string $fullName): array
    {
        $parts = preg_split('/\s+/', trim($fullName));

        if (count($parts) === 0) {
            return ['first_name' => '', 'last_name' => ''];
        }

        $firstName = array_shift($parts); // Get first element
        $lastName = implode(' ', $parts); // Join the remaining elements

        return [
            'first_name' => $firstName,
            'last_name' => $lastName
        ];
    }

    /**
     * Formats a string by replacing underscores with spaces.
     *
     * @param string $label
     * @return string
     */
    public static function formatLabel(string $label): string
    {
        return str_replace('_', ' ', $label);
    }

    /**
     * Censors a string by showing the first few characters and replacing the rest with asterisks.
     * Useful for sensitive data like NIK or addresses.
     *
     * @param string|null $data The string to be censored.
     * @param int $visibleChars The number of characters to keep visible from the start.
     * @return string The censored string or an empty string if input is null/empty.
     */
    public static function censorData(?string $data, int $visibleChars = 4): string
    {
        if (empty($data)) return '';
        return substr($data, 0, $visibleChars) . str_repeat('*', strlen($data) - $visibleChars);
    }
}
