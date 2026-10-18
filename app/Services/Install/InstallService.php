<?php

namespace App\Services\Install;

use App\Repositories\Install\InstallRepository;
use Database\Seeders\CatalogProductSeeder;
use Database\Seeders\DemoUsersSeeder;
use Database\Seeders\SmooveHubDatabaseSeeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use PDO;
use PDOException;
use RuntimeException;
use Throwable;

class InstallService
{
    public function __construct(
        private readonly InstallRepository $repository,
    ) {}

    /**
     * @return array{installed: bool, ready: bool, checks: list<array{id: string, label: string, passed: bool, detail: string}>}
     */
    public function status(): array
    {
        $requirements = $this->repository->requirements();

        return [
            'installed' => $this->repository->isInstalled(),
            'ready' => $requirements['ready'],
            'checks' => $requirements['checks'],
        ];
    }

    public function isInstalled(): bool
    {
        return $this->repository->isInstalled();
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array{admin_email: string, redirect: string}
     */
    public function run(array $payload): array
    {
        if ($this->repository->isInstalled()) {
            throw new RuntimeException('SmooveHub is already installed.');
        }

        $requirements = $this->repository->requirements();

        if (! $requirements['ready']) {
            throw new RuntimeException('Server requirements are not met. Fix failed checks and try again.');
        }

        try {
            $this->writeEnvironment($payload);
            $this->reloadDatabaseConfig($payload);
            $this->testDatabaseConnection($payload);
            $this->generateAppKeyIfNeeded();
            $this->runMigrations();
            $this->runSeeders();
            $admin = $this->repository->upsertAdmin([
                'admin_name' => $payload['admin_name'],
                'admin_email' => $payload['admin_email'],
                'admin_password' => $payload['admin_password'],
            ]);
            $this->createStorageLink();
            $this->repository->markInstalled();

            return [
                'admin_email' => $admin->email,
                'redirect' => url('/admin'),
            ];
        } catch (Throwable $e) {
            throw new RuntimeException($this->friendlyError($e), previous: $e);
        }
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function writeEnvironment(array $payload): void
    {
        $this->repository->updateEnv([
            'APP_NAME' => 'SmooveHub',
            'APP_URL' => rtrim((string) $payload['app_url'], '/'),
            'DB_CONNECTION' => 'mysql',
            'DB_HOST' => (string) $payload['db_host'],
            'DB_PORT' => (string) $payload['db_port'],
            'DB_DATABASE' => (string) $payload['db_database'],
            'DB_USERNAME' => (string) $payload['db_username'],
            'DB_PASSWORD' => (string) ($payload['db_password'] ?? ''),
        ]);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function reloadDatabaseConfig(array $payload): void
    {
        Artisan::call('config:clear');

        Config::set('database.default', 'mysql');
        Config::set('database.connections.mysql.host', $payload['db_host']);
        Config::set('database.connections.mysql.port', $payload['db_port']);
        Config::set('database.connections.mysql.database', $payload['db_database']);
        Config::set('database.connections.mysql.username', $payload['db_username']);
        Config::set('database.connections.mysql.password', $payload['db_password'] ?? '');

        DB::purge('mysql');
        DB::reconnect('mysql');
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function testDatabaseConnection(array $payload): void
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s',
            $payload['db_host'],
            $payload['db_port'],
            $payload['db_database'],
        );

        try {
            new PDO(
                $dsn,
                (string) $payload['db_username'],
                (string) ($payload['db_password'] ?? ''),
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        } catch (PDOException $e) {
            throw new RuntimeException('Could not connect to MySQL: '.$e->getMessage(), previous: $e);
        }

        DB::connection('mysql')->getPdo();
    }

    private function generateAppKeyIfNeeded(): void
    {
        if ($this->repository->appKeyIsEmpty() || blank(config('app.key'))) {
            Artisan::call('key:generate', ['--force' => true]);
        }
    }

    private function runMigrations(): void
    {
        $exit = Artisan::call('migrate', ['--force' => true]);

        if ($exit !== 0) {
            throw new RuntimeException(trim(Artisan::output()) ?: 'Migration failed.');
        }
    }

    private function runSeeders(): void
    {
        Artisan::call('db:seed', [
            '--class' => SmooveHubDatabaseSeeder::class,
            '--force' => true,
        ]);

        Artisan::call('db:seed', [
            '--class' => DemoUsersSeeder::class,
            '--force' => true,
        ]);

        Artisan::call('db:seed', [
            '--class' => CatalogProductSeeder::class,
            '--force' => true,
        ]);
    }

    private function createStorageLink(): void
    {
        if (! file_exists(public_path('storage'))) {
            Artisan::call('storage:link');
        }
    }

    private function friendlyError(Throwable $e): string
    {
        $message = trim($e->getMessage());

        return $message !== '' ? $message : 'Installation failed. Check server logs and try again.';
    }
}
