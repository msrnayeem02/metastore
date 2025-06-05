<?php

namespace App\Services;

use App\Models\DeliveryPartner;
use Illuminate\Support\Facades\Crypt;

class CourierCredentialService
{
    /**
     * Decrypt credentials for a specific courier slug.
     */
    public static function decrypt(string $slug): array|string
    {
        $config = config('courier');

        // 1. Found in config
        if (isset($config[$slug])) {
            $decrypted = self::decryptCredentials($config[$slug]);

            // Check if missing in DB, then insert
            $partner = DeliveryPartner::where('slug', $slug)->first();
            if (!$partner || !$partner->credentials) {
                DeliveryPartner::updateOrCreate(
                    ['slug' => $slug],
                    ['credentials' => $config[$slug]]
                );
            }

            return $decrypted;
        }

        // 2. Not in config → check database
        $partner = DeliveryPartner::where('slug', $slug)->first();
        if ($partner && $partner->credentials) {
            // Save encrypted creds to config file
            self::writeToConfig($slug, $partner->credentials);
            return self::decryptCredentials($partner->credentials);
        }

        // 3. Not found anywhere
        return 'No credentials found.';
    }

    /**
     * Decrypt the credentials array.
     */
    private static function decryptCredentials(array $encryptedCredentials): array
    {
        $decrypted = [];

        foreach ($encryptedCredentials as $key => $encryptedValue) {
            try {
                $decrypted[$key] = Crypt::decryptString($encryptedValue);
            } catch (\Exception $e) {
                $decrypted[$key] = 'Invalid or not encrypted';
            }
        }

        return $decrypted;
    }

    /**
     * Store (encrypt and save) credentials to both config and DB.
     */
    public static function store(string $slug, array $fields): void
    {
        $encryptedFields = collect($fields)->map(fn($v) => Crypt::encryptString($v))->toArray();

        self::writeToConfig($slug, $encryptedFields);

        DeliveryPartner::updateOrCreate(
            ['slug' => $slug],
            ['credentials' => $encryptedFields]
        );
    }

    /**
     * Internal: Write encrypted credentials to the config file.
     */
    private static function writeToConfig(string $slug, array $encryptedFields): void
    {
        $configPath = config_path('courier.php');
        $existing = file_exists($configPath) ? include($configPath) : [];

        $existing[$slug] = $encryptedFields;

        $content = "<?php\n\nreturn [\n";
        foreach ($existing as $partner => $keys) {
            $content .= "    '{$partner}' => [\n";
            foreach ($keys as $key => $value) {
                $escaped = str_replace("'", "\\'", $value);
                $content .= "        '{$key}' => '{$escaped}',\n";
            }
            $content .= "    ],\n";
        }
        $content .= "];\n";

        file_put_contents($configPath, $content);
    }
}
