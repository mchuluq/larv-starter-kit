<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

use Illuminate\Contracts\Foundation\Application;

use Illuminate\Support\Facades\Storage;
use Illuminate\Encryption\Encrypter;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Log;
use League\Flysystem\Filesystem;
use Swis\Flysystem\Encrypted\EncryptedFileSystemAdapter;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Passport::ignoreRoutes();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        try {
            // local encrypted storage
            Storage::extend('encrypted',function(Application $app, array $config){
                $local = new \League\Flysystem\Local\LocalFilesystemAdapter($config['root']);
                $encrypter = new Encrypter($config['key'],strtolower($config['cipher']));
                $adapter = new EncryptedFileSystemAdapter($local,$encrypter);
                
                return new FilesystemAdapter(new Filesystem($adapter, $config),$adapter,$config);
            });
            
            // encrypted google drive storage
            Storage::extend('google',function(Application $app, array $config){
                $options = [];
                if (!empty($config['teamDriveId'] ?? null)) {
                    $options['teamDriveId'] = $config['teamDriveId'];
                }

                $client = new \Google\Client();
                $client->setClientId($config['clientId']);
                $client->setClientSecret($config['clientSecret']);
                $client->refreshToken($config['refreshToken']);
                
                $service = new \Google\Service\Drive($client);
                $adapter = new \Masbug\Flysystem\GoogleDriveAdapter($service, $config['folderId'] ?? '/', $options);
                
                $encrypter = new Encrypter($config['key'],strtolower($config['cipher']));
                $proxy_adapter = new EncryptedFileSystemAdapter($adapter,$encrypter);

                $driver = new \League\Flysystem\Filesystem($proxy_adapter);

                return new \Illuminate\Filesystem\FilesystemAdapter($driver, $proxy_adapter);
            });
        }catch(\Exception $e){
            Log::error($e->getMessage());
        }
    }
}
