<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Helpers\Crypter;

class EncryptRequestResponse{

    protected $crypter;
    protected $force;

    public function __construct(){
        $this->crypter = Crypter::make();
    }

    public function handle(Request $request, Closure $next,$force=null){
        $this->force = ($force == 'force');
        if ($request->header('X-REQUEST-ENCRYPTED')) {
            $this->modifyRequest($request);
        }
        $response = $next($request);
        if ($response instanceof JsonResponse) {
            $this->modifyResponse($request, $response);
        }
        return $response;
    }

    protected function modifyRequest(Request $request){
        $decrypted = $request->payload ? $this->crypter->decrypt($request->payload) : null;
        if ($decrypted) {
            $request->merge($decrypted);
            $request->replace($request->except('payload'));
        }
    }

    protected function modifyResponse(Request $request, JsonResponse $response){
        if ($request->header('X-RESPONSE-ENCRYPTED') || $this->force) {
            $payload = ['payload' => $this->crypter->encrypt(json_decode($response->content(), true)),'secure'=>true];
            $response->setContent(json_encode($payload));
            $response->header('X-RESPONSE-ENCRYPTED', "1");
        }
    }
    
}