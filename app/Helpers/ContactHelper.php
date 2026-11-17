<?php

namespace App\Helpers;

class ContactHelper
{
    /**
     * Format Telegram link supporting both usernames and phone numbers
     */
    public static function formatTelegramUrl(?string $val): string
    {
        if (empty($val)) {
            return '';
        }

        $val = trim($val);

        if (str_starts_with($val, 'http://') || str_starts_with($val, 'https://')) {
            return $val;
        }

        if (str_starts_with($val, 't.me/')) {
            return 'https://' . $val;
        }

        $clean = ltrim($val, '@');
        $cleanDigits = preg_replace('/[^0-9]/', '', $clean);

        // If it's a phone number (e.g. +37529... or 37529... or 8029... without alphabetical chars)
        if (preg_match('/^\+?[0-9]{9,15}$/', $clean) || (strlen($cleanDigits) >= 9 && strlen($cleanDigits) <= 13 && !preg_match('/[a-zA-Z]/', $clean))) {
            if (str_starts_with($cleanDigits, '80') && strlen($cleanDigits) == 11) {
                $cleanDigits = '375' . substr($cleanDigits, 2);
            } elseif (strlen($cleanDigits) == 9) {
                $cleanDigits = '375' . $cleanDigits;
            }
            return 'tg://resolve?phone=' . $cleanDigits;
        }

        return 'https://t.me/' . $clean;
    }

    /**
     * Format WhatsApp link
     */
    public static function formatWhatsappUrl(?string $val): string
    {
        if (empty($val)) {
            return '';
        }

        $val = trim($val);

        if (str_starts_with($val, 'http://') || str_starts_with($val, 'https://')) {
            return $val;
        }

        if (str_starts_with($val, 'wa.me/')) {
            return 'https://' . $val;
        }

        $cleanPhone = preg_replace('/[^0-9]/', '', $val);
        if (str_starts_with($cleanPhone, '80') && strlen($cleanPhone) == 11) {
            $cleanPhone = '375' . substr($cleanPhone, 2);
        } elseif (strlen($cleanPhone) == 9) {
            $cleanPhone = '375' . $cleanPhone;
        }

        return 'https://wa.me/' . $cleanPhone;
    }

    /**
     * Format Viber link
     */
    public static function formatViberUrl(?string $val): string
    {
        if (empty($val)) {
            return '';
        }

        $val = trim($val);

        if (str_starts_with($val, 'viber://') || str_starts_with($val, 'http://') || str_starts_with($val, 'https://')) {
            return $val;
        }

        $cleanPhone = preg_replace('/[^0-9]/', '', $val);
        if (str_starts_with($cleanPhone, '80') && strlen($cleanPhone) == 11) {
            $cleanPhone = '375' . substr($cleanPhone, 2);
        } elseif (strlen($cleanPhone) == 9) {
            $cleanPhone = '375' . $cleanPhone;
        }

        return 'viber://chat?number=%2B' . $cleanPhone;
    }
}
