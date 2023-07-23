<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller {

    public function file(Request $req, $filepath){
        $disk = config('filesystems.default');
        $file = Storage::disk($disk)->exists($filepath);
        if($file){
            $mode = $req->query('mode','view');
            if($mode=='download'){
                return Storage::disk($disk)->download($filepath);    
            }else{
                return Storage::disk($disk)->response($filepath);
            }
        }else{
            abort(404);
        }
    }

}