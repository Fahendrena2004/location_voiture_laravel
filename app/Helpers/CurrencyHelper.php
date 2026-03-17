<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Session;

class CurrencyHelper
{
    const RATE_EUR_TO_MGA = 5000;

    /**
     * Format an amount based on the current currency in session.
     *
     * @param float|int $amount
     * @return string
     */
    public static function format($amount)
    {
        $currency = Session::get('currency', 'EUR');

        if ($currency === 'MGA') {
            $converted = $amount * self::RATE_EUR_TO_MGA;
            return number_format($converted, 0, '.', ' ') . ' Ar';
        }

        return number_format($amount, 2, ',', ' ') . ' €';
    }

    /**
     * Get the current currency code.
     *
     * @return string
     */
    public static function getCurrency()
    {
        return Session::get('currency', 'EUR');
    }
}
