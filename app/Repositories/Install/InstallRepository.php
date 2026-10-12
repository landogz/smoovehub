<?php

namespace App\Repositories\Install;

use App\Models\User;
use Illuminate\Support\Facades\File;

class InstallRepository
{
    public function lockPath(): string
    {
        return storage_path('app/installed');
    }

    public function isInstalled(): bool
    {
        return File::exists($this->lockPath());
    }

    public function markInstalled(): void
    {
        File::ensureDirectoryExists(dirname($this->lockPath()));
        File::put($this->lockPath(), now()->toIso8601String());
    }

    /**
     * @return array{ready: bool, checks: list<array{id: string, label: string, passed: bool, detail: string}>}
     */
    public function requirements(): array
    {
        $envPath = base_path('.env');
        $envWritable = File::exists($envPath)
            ? is_writable($envPath)
            : is_writable(base_path());

        $checks = [
            [
                'id' => 'php',
                'label' => 'PHP 8.3+',
                'passed' => version_compare(PHP_VERSION, '8.3.0', '>='),
                'detail' => 'Current: '.PHP_VERSION,
            ],
            [
                'id' => 'pdo_mysql',
                'label' => 'PDO MySQL extension',
                'passed' => extension_loaded('pdo_mysql'),
                'detail' => extension_loaded('pdo_mysql') ? 'Enabled' : 'Missing',
            ],
            [
                'id' => 'openssl',
                'label' => 'OpenSSL extension',
                'passed' => extension_loaded('openssl'),
                'detail' => extension_loaded('openssl') ? 'Enabled' : 'Missing',
            ],
            [
                'id' => 'mbstring',
                'label' => 'Mbstring extension',
                'passed' => extension_loaded('mbstring'),
                'detail' => extension_loaded('mbstring') ? 'Enabled' : 'Missing',
            ],
            [
                'id' => 'tokenizer',
                'label' => 'Tokenizer extension',
                'passed' => extension_loaded('tokenizer'),
                'detail' => extension_loaded('tokenizer') ? 'Enabled' : 'Missing',
            ],
            [
                'id' => 'json',
                'label' => 'JSON extension',
                'passed' => extension_loaded('json'),
                'detail' => extension_loaded('json') ? 'Enabled' : 'Missing',
            ],
            [
                'id' => 'curl',
                'label' => 'cURL extension',
                'passed' => extension_loaded('curl'),
                'detail' => extension_loaded('curl') ? 'Enabled' : 'Missing',
            ],
            [
                'id' => 'storage',
                'label' => 'storage/ writable',
                'passed' => is_writable(storage_path()),
                'detail' => is_writable(storage_path()) ? 'Writable' : 'Not writable',
            ],
            [
                'id' => 'bootstrap_cache',
                'label' => 'bootstrap/cache writable',
                'passed' => is_writable(base_path('bootstrap/cache')),
                'detail' => is_writable(base_path('bootstrap/cache')) ? 'Writable' : 'Not writable',
            ],
            [
                'id' => 'env',
                'label' => '.env writable',
                'passed' => $envWritable,
                'detail' => $envWritable ? 'Writable' : 'Not writable',
            ],
        ];

        return [
            'ready' => collect($checks)->every(fn (array $check) => $check['passed']),
            'checks' => $checks,
        ];
    }

    public function ensureEnvFile(): void
    {
        $envPath = base_path('.env');

        if (! File::exists($envPath)) {
            File::copy(base_path('.env.example'), $envPath);
        }
    }

    /**
     * @param  array<string, string|null>  $values
     */
    public function updateEnv(array $values): void
    {
        $this->ensureEnvFile();

        $envPath = base_path('.env');
        $content = File::get($envPath);

        foreach ($values as $key => $value) {
            $normalized = $this->formatEnvValue($value);
            $pattern = "/^{$key}=.*$/m";

            if (preg_match($pattern, $content)) {
                $content = preg_replace($pattern, "{$key}={$normalized}", $content) ?? $content;
            } else {
                $content = rtrim($content).PHP_EOL."{$key}={$normalized}".PHP_EOL;
            }
        }

        File::put($envPath, $content);
    }

    public function appKeyIsEmpty(): bool
    {
        $key = (string) config('app.key');

        return $key === '' || $key === 'base64:';
    }

    /**
     * @param  array{admin_name: string, admin_email: string, admin_password: string}  $payload
     */
    public function upsertAdmin(array $payload): User
    {
        $user = User::query()->updateOrCreate(
            ['email' => $payload['admin_email']],
            [
                'name' => $payload['admin_name'],
                'password' => $payload['admin_password'],
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        $user->assignRole('super-admin');

        return $user;
    }

    private function formatEnvValue(?string $value): string
    {
        $value ??= '';

        if ($value === '') {
            return '';
        }

        if (preg_match('/\s|#|"|\'/', $value)) {
            return '"'.str_replace('"', '\"', $value).'"';
        }

        return $value;
    }
}
