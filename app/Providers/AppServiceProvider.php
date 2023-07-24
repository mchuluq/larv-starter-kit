<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

use Illuminate\Contracts\Foundation\Application;

use Illuminate\Support\Facades\Storage;
use Illuminate\Encryption\Encrypter;
use Illuminate\Filesystem\FilesystemAdapter;
use League\Flysystem\Filesystem;

use Swis\Flysystem\Encrypted\EncryptedFileSystemAdapter;

use Illuminate\Support\Collection;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Passport::ignoreRoutes();

        $this->registerMacros();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Storage::extend('encrypted',function(Application $app, array $config){
            $local = new \League\Flysystem\Local\LocalFilesystemAdapter($config['root']);
            $encrypter = new Encrypter($config['key'],strtolower($config['cipher']));
            $adapter = new EncryptedFileSystemAdapter($local,$encrypter);
            
            return new FilesystemAdapter(new Filesystem($adapter, $config),$adapter,$config);
        });
    }

    
    protected function registerMacros(){
        Collection::make(glob(__DIR__ . '/../Macros/*.php'))->mapWithKeys(function ($path) {
            return [$path => pathinfo($path, PATHINFO_FILENAME)];
        })->each(function ($macro, $path) {
            require_once $path;
        });
    }
}
