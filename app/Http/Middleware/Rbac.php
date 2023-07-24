<?php

namespace App\Libraries\Rbac\Http\Middlewares;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\Concerns\HasParameters;

class Authenticate {

    use HasParameters;

    protected $authenticated = false;
    protected $has_account = false;    
    protected $has_token = false;

    public function __construct(){
        $this->authenticated = Auth::check();
        $this->has_account = ($this->authenticated) ? Auth::user()->account : false;
        $this->has_token = ($this->authenticated) ? request()->user()->token() : false;
    }

    public function handle($request, Closure $next, $route=null){
        $request->headers->set('Accept', 'application/json');
        if (!$this->authenticated) {
            return $this->setAbortResponse($request);
        }elseif (!$this->has_account) {
            return $this->setSelectAccount($request);
        }else{
            $route = $route ?? $request->route()->getAction('as');
            $permissions = $request->user()->getRoutePermissions() ?? [];
            if(!$route){
                return $this->setRoutePermissionRequired($request);
            }elseif(in_array($route,$permissions)){
                return $next($request);
            }
            return $this->setRoutePermissionRequired($request);
        };
    }

    protected function setAbortResponse($request){
        if($this->has_token || $request->wantJson()){
            return response()->json([
                'code' => 'AUTH_REQUIRED',
                'message' => __('auth.auth_required'),
            ], 401);
        }else{
            return redirect('login');
        }
    }

    protected function setSelectAccount($request){
        if($this->has_token || $request->wantJson()){
            $user = $request->user();
            $accounts = ($user->account_id) ? $user->accounts(true)->pluck('accountable_id','id') : [];
            return response()->json([
                'code' => 'ACCOUNT_SELECT_REQUIRED',
                'message' => __('auth.account_select_required'),
                'accounts' => $accounts
            ],403);
        }else{
            return redirect('select.account');
        }
    }

    protected function setRoutePermissionRequired($request){
        if ($this->has_token || $request->wantJson()) {
            return response()->json([
                'code' => 'INSUFFICIENT_PERMISSION',
                'message' => __('auth.insufficient_permission')
            ], 403);
        } else {
            return abort(403,__('auth.insufficient_permission'));
        }
    }
}