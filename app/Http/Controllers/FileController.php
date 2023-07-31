<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller {

    public function file(Request $req,$disk,$filepath){
        $disk = (!$disk) ? config('filesystems.default') : $disk;
        $disk_list = array_keys(config('filesystems.disks'));
        if(!in_array($disk,$disk_list)){
            abort(404);
        }
        $exists = Storage::disk($disk)->exists($filepath);
        if($exists){
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