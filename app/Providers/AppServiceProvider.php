<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Vite;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Beri tahu Laravel secara fisik folder public-nya pindah
        // /home/panganlestariid/laravel_app/larcrm/public/build//home/panganlestariid/public_html/subdom/salesvisit.panganlestari.id/build/manifest.jso
        /*
        $this->app->bind('path.public', function () {
            return '/home/panganlestariid/public_html/subdom/salesvisit.panganlestari.id';
        });
        */
    }

    public function boot(): void
    {
        // Hapus Vite::useManifestFilename yang lama.
        // Kita gunakan cara ini agar Vite tidak menambahkan prefix path default.
        
        /*
        Vite::useBuildDirectory('build');
        
        // Paksa hot reload file manifest secara manual jika berada di production
        if (app()->environment('production')) {
            $manifestPath = '/home/panganlestariid/public_html/subdom/salesvisit.panganlestari.id/build/manifest.json';
            Vite::useManifestFilename($manifestPath);
        }
        */
    }
}