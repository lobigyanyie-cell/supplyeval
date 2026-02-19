<?php

namespace App\Config;

class Paystack
{
    // Paystack Keys - Use Environment Variables or Config files in production
    // These are test keys. Replace with live keys when ready.
    public static function getPublicKey()
    {
        return Settings::get('paystack_public_key', 'pk_test_7da18c9a8ae1672518fd710ac639ca89644f013d');
    }

    public static function getSecretKey()
    {
        return Settings::get('paystack_secret_key', 'sk_test_dbd73f4b79bd6ec0561d3ec56b7e0c5b5f746117');
    }
}
