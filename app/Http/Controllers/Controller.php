<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Validation\ValidationException;

class Controller extends BaseController{
    
    use AuthorizesRequests, ValidatesRequests;

    protected function message($msg,$type='success',$data=[],$trace=null,$code=200){
        $defaults = array(
            'message' => null,
            'type' => 'success',
            'title' => 'message',
            'code' => $code,
            'trace' => $trace,
            'data' => $data
        );
        if(is_array($msg)){
            $resp = array_merge($defaults,$msg);
        }else{
            $row['message'] = $msg;
            $row['type'] = $type;
            $row['data'] = $data;
            $resp = array_merge($defaults,$row);
        }
        return response()->json($resp,$resp['code']);
    }

    protected function errorMessage(string $lang_key,array $lang_data=[],\Exception $error=null,$data=[]){
        if($error instanceof ValidationException){
            return $this->validationMessage($error,$data);
        }elseif($error instanceof \Exception){
            $lang_data['text'] = $error->getMessage();
            $trace = (env('APP_DEBUG',false) == true) ? $error->getTrace() : null; 
            $data['code'] = 403;
            return $this->message(__($lang_key,$lang_data),'error',$data,$trace,$data['code']);
        }else{
            $data['code'] = 403;
            return $this->message(__($lang_key,$lang_data),'error',$data,null,$data['code']);
        }
    }

    protected function validationMessage(\Illuminate\Validation\ValidationException $e,$data=null){
        $errors = [];
        foreach($e->errors() as $key=>$err){
            $errors[str_replace('.','_',$key)] = $err;
        }
        $data = [
            'message' => $e->getMessage(),
            'errors' => $errors,
            'type' => 'error',
            'code' => 422,
            'data' => $data
        ];
        return $this->message($data,'error',$data,null,422);
    }
}
