<?php

namespace App\Helpers;

/**
 * Display-only pricing helpers.
 *
 * The app stores `premium_price` as an amount in the selected Settings currency (GHS/USD/NGN).
 * This helper formats the price for UI and provides a clear charge notice.
 */
class PricingDisplay
{
    /** 1 USD = N GHS (demo rate for UI only). */
    public const GHS_PER_USD = 12.0;

    /**
     * @param float|string $amount Value from settings (e.g. premium_price), in Settings currency
     * @param string $currency Currency code (GHS/USD/NGN)
     * @return array{
     *   ghs: float,
     *   usd: float,
     *   usd_per_month: string,
     *   ghs_billed_line: string,
     *   charge_notice: string,
     *   disclaimer: string
     * }
     */
    public static function formatMonthly(float|string $amount, string $currency = 'GHS'): array
    {
        $currency = strtoupper(trim($currency));
        $value = max(0.0, (float) $amount);

        $ghs = 0.0;
        $usd = 0.0;
        $chargeNotice = '';
        $disclaimer = '';
        $ghsLine = '';

        if ($currency === 'USD') {
            $usd = $value;
            $ghs = $usd * self::GHS_PER_USD;
            $chargeNotice = 'You will be charged $' . number_format($usd, 2) . ' (USD)';
            $ghsLine = '≈ ₵' . number_format($ghs, 2) . ' equivalent (display only)';
            $disclaimer = '*Payments processed in USD.';
        } elseif ($currency === 'GHS') {
            $ghs = $value;
            $usd = $ghs > 0 ? $ghs / self::GHS_PER_USD : 0.0;
            $ghsDecimals = (floor($ghs) === $ghs) ? 0 : 2;
            $chargeNotice = 'You will be charged ₵' . number_format($ghs, $ghsDecimals) . ' (GHS)';
            $ghsLine = '₵' . number_format($ghs, $ghsDecimals) . ' billed in GHS';
            $disclaimer = '*Prices shown in USD. Payments processed in GHS.';
        } else {
            // For other currencies, show value as-is and no conversion line.
            $chargeNotice = 'You will be charged ' . $currency . ' ' . number_format($value, 2);
            $disclaimer = '*Payments processed in ' . $currency . '.';
        }

        return [
            'ghs' => $ghs,
            'usd' => $usd,
            'usd_per_month' => '$' . number_format($usd, 2) . '/mo',
            'ghs_billed_line' => $ghsLine,
            'charge_notice' => $chargeNotice,
            'disclaimer' => $disclaimer,
        ];
    }
}
