<?php

namespace App\Services;

use App\Models\PaymentGateway;
use Illuminate\Support\Facades\Crypt;

class PaymentCredentialService
{
    /**
     * Get credentials for a specific payment gateway.
     */
    public static function getCredentials(string $gateway): array|string
    {
        // Special handling for SSLCommerz - merge DB and config
        if ($gateway === 'sslcommerz') {
            return self::getSslCommerzCredentials();
        }

        // First, check if credentials exist in database
        $paymentGateway = PaymentGateway::where('gateway', $gateway)->where('status', 1)->first();

        if ($paymentGateway && $paymentGateway->config) {
            $config = is_array($paymentGateway->config)
                ? $paymentGateway->config
                : json_decode($paymentGateway->config, true);

            return $config;
        }

        // Check different config files based on gateway as fallback
        if ($gateway === 'bkash') {
            $config = config('bkash');
            if (!empty($config)) {
                return $config;
            }
        } elseif ($gateway === 'nagad') {
            $config = config('nagad');
            if (!empty($config)) {
                return $config;
            }
        }

        return 'No credentials found.';
    }

    /**
     * Store credentials to payment gateway config and database.
     */
    public static function store(string $gateway, array $fields): void
    {
        // Store directly without encryption

        // Update database first
        PaymentGateway::updateOrCreate(
            ['gateway' => $gateway],
            ['config' => json_encode($fields), 'status' => 1]
        );

        // Store in the appropriate config file based on gateway
        $configPath = '';
        $configArray = [];

        if ($gateway === 'sslcommerz') {
            self::storeSslCommerzCredentials($fields);
        } elseif ($gateway === 'bkash') {
            $configPath = config_path('bkash.php');
            $configArray = $fields;
        } elseif ($gateway === 'nagad') {
            $configPath = config_path('nagad.php');
            $configArray = $fields;
        } else {
            // For other gateways, create a payments.php config file
            $configPath = config_path('payments.php');
            $existing = file_exists($configPath) ? include($configPath) : [];
            $existing[$gateway] = $fields;
            $configArray = $existing;
        }

        if (!empty($configPath)) {
            // Format and write the config file
            self::writeConfigFile($configPath, $configArray);
        }
    }

    /**
     * Write the config file with proper formatting.
     */
    private static function writeConfigFile(string $path, array $configArray): void
    {
        $content = "<?php\n\nreturn " . self::exportArray($configArray, 0) . ";\n";
        file_put_contents($path, $content);
    }

    /**
     * Export array with proper indentation.
     */
    private static function exportArray(array $array, int $depth): string
    {
        $indent = str_repeat('    ', $depth);
        $nextIndent = str_repeat('    ', $depth + 1);
        $export = "[\n";

        foreach ($array as $key => $value) {
            $export .= $nextIndent . "'" . str_replace("'", "\\'", $key) . "' => ";

            if (is_array($value)) {
                $export .= self::exportArray($value, $depth + 1);
            } else {
                $export .= "'" . str_replace("'", "\\'", $value) . "'";
            }

            $export .= ",\n";
        }

        $export .= $indent . "]";
        return $export;
    }

    /**
     * Special handling for SSLCommerz credentials
     */
    private static function getSslCommerzCredentials(): array
    {
        // Get base config from file
        $configFile = config('sslcommerz');
        $credentials = $configFile['apiCredentials'] ?? [];

        // Check DB for overrides
        $dbGateway = PaymentGateway::where('gateway', 'sslcommerz')->where('status', 1)->first();

        if ($dbGateway) {
            $dbConfig = is_array($dbGateway->config)
                ? $dbGateway->config
                : json_decode($dbGateway->config, true);

            // Override config file values with DB values
            if (isset($dbConfig['store_id'])) {
                $credentials['store_id'] = $dbConfig['store_id'];
            }

            if (isset($dbConfig['store_password'])) {
                $credentials['store_password'] = $dbConfig['store_password'];
            }

            // Add any additional fields from DB that aren't in config
            foreach ($dbConfig as $key => $value) {
                if (!isset($credentials[$key])) {
                    $credentials[$key] = $value;
                }
            }
        }

        return $credentials;
    }

    /**
     * Store credentials for SSLCommerz
     */
    public static function storeSslCommerzCredentials(array $fields): void
    {
        // Update database
        PaymentGateway::updateOrCreate(
            ['gateway' => 'sslcommerz'],
            ['config' => json_encode($fields), 'status' => 1]
        );

        // Update config file
        $configPath = config_path('sslcommerz.php');
        $existing = file_exists($configPath) ? include($configPath) : [];

        // Update the apiCredentials section
        $existing['apiCredentials'] = [
            'store_id' => $fields['store_id'],
            'store_password' => $fields['store_password'],
        ];

        // Set testmode if provided
        if (isset($fields['sandbox'])) {
            $existing['testmode'] = $fields['sandbox'] ? true : false;
        }

        // Write updated config back to file
        self::writeConfigFile($configPath, $existing);
    }
}
