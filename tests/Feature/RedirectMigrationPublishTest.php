<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use Rias\StatamicRedirect\RedirectServiceProvider;

it('reuses existing redirect migration filenames when publishing', function () {
    $migrationsPath = database_path('migrations');

    $existingMigrations = [
        $migrationsPath.'/2020_01_01_000000_create_redirect_redirects_table.php',
        $migrationsPath.'/2020_01_01_000001_add_description_to_redirect_redirects_table.php',
        $migrationsPath.'/2020_01_01_000002_increase_redirect_redirects_table_url_length.php',
    ];

    try {
        File::ensureDirectoryExists($migrationsPath);

        foreach ($existingMigrations as $migration) {
            File::put($migration, '<?php');
        }

        (new RedirectServiceProvider($this->app))->boot();

        $paths = ServiceProvider::pathsToPublish(
            RedirectServiceProvider::class,
            'statamic-redirect-redirect-migrations'
        );

        expect($paths[base_path('database/migrations/create_redirect_redirects_table.php.stub')])->toBe($existingMigrations[0]);
        expect($paths[base_path('database/migrations/add_description_to_redirect_redirects_table.php.stub')])->toBe($existingMigrations[1]);
        expect($paths[base_path('database/migrations/increase_redirect_redirects_table_url_length.php.stub')])->toBe($existingMigrations[2]);
    } finally {
        File::delete($existingMigrations);
    }
});
