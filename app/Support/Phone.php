<?php

namespace App\Support;

class Phone
{
    /** Met un numéro sénégalais au format +221XXXXXXXXX. */
    public static function normalize(?string $phone): string
    {
        $digits = preg_replace('/\D/', '', (string) $phone);

        if (str_starts_with($digits, '00221')) {
            $digits = substr($digits, 5);
        } elseif (str_starts_with($digits, '221') && strlen($digits) === 12) {
            $digits = substr($digits, 3);
        }

        return '+221' . $digits;
    }
}