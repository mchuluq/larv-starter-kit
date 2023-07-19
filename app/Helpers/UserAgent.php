<?php namespace App\Helpers;

use DeviceDetector\DeviceDetector;

class UserAgent {

    public static function parse($user_agent){
        if(!$user_agent){
            return [];
        }
        $device = new DeviceDetector($user_agent);
        $device->setCache(new \DeviceDetector\Cache\LaravelCache());
        $device->parse();
        return [
            'os' => $device->getOs('name').' '.$device->getOs('version'),
            'device' => $device->getDeviceName(),
            'client' => $device->getClient('name').' '.$device->getClient('version'),
            'brand' => $device->getBrandName(),
            'model' => $device->getModel(),
        ];
    }

}