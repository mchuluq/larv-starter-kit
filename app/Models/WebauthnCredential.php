<?php namespace App\Models;

use DeviceDetector\Parser\Device\AbstractDeviceParser;
use DeviceDetector\DeviceDetector;

class WebauthnCredential extends \Laragear\WebAuthn\Models\WebauthnCredential {

    protected $visible = ['id', 'origin', 'alias', 'aaguid', 'attestation_format', 'disabled_at','counter','user_agent','created_at','updated_at','user_device'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = ['user_device'];

    public function getUserDeviceAttribute(){
        $agent = $this->user_agent;
        // $agent = "Mozilla/5.0 (Linux; Android 13; SM-A336E Build/TP1A.220624.014; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/115.0.5790.21 Mobile Safari/537.36";
        $device = new DeviceDetector($agent);
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