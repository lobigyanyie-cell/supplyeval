<?php

namespace App\Helpers;

/**
 * Display-only pricing: GHS (stored) ↔ USD using a fixed demo rate.
 * Does not affect Paystack amounts or database values.
 */
class PricingDisplay
{
    /** 1 USD = N GHS (demo rate for UI only). */
    public const GHS_PER_USD = 12.0;

    /**
     * @param float|string $ghsAmount Value from settings (e.g. premium_price), in GHS
     * @return array{
     *   ghs: float,
     *   usd: float,
     *   usd_per_month: string,
     *   ghs_billed_line: string,
     *   charge_notice: string,
     *   disclaimer: string
     * }
     */
    public static function formatMonthly(float|string $ghsAmount): array
    {
        $ghs = max(0.0, (float) $ghsAmount);
        $usd = $ghs > 0 ? $ghs / self::GHS_PER_USD : 0.0;
        $ghsDecimals = (floor($ghs) === $ghs) ? 0 : 2;

        return [
            'ghs' => $ghs,
            'usd' => $usd,
            'usd_per_month' => '$' . number_format($usd, 2) . '/mo',
            'ghs_billed_line' => '≈ ₵' . number_format($ghs, $ghsDecimals) . ' billed in GHS',
            'charge_notice' => 'You will be charged ₵' . number_format($ghs, $ghsDecimals) . ' (GHS)',
            'disclaimer' => '*Prices shown in USD. Payments processed in GHS.',
        ];
    }
}
