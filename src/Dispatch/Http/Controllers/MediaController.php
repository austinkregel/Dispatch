<?php

namespace Kregel\Dispatch\Http\Controllers;

use Illuminate\Http\Request;
use Kregel\Dispatch\Models\Jurisdiction;
use Kregel\Dispatch\Models\Photos;
use Kregel\FormModel\FormModel;

class MediaController extends Controller
{
    public function showMedia($uuid){
        if(stripos($uuid, '.') !== false){
            $tmp  = explode('.', $uuid);
            $uuid = $tmp[0];
        }
        $media = Photos::whereUuid($uuid)->first();
        if(empty($media) || $uuid === '159a50e6-5382-4a52-ae94-3f5a4e8f0584'){
            return response()->make(file_get_contents(storage_path('app/media/159a50e6-5382-4a52-ae94-3f5a4e8f0584.jpeg')), 404)->header('Content-type','image/jpeg')->header('Content-length', filesize((storage_path('app/media/159a50e6-5382-4a52-ae94-3f5a4e8f0584.jpeg'))));
        }
        if(auth()->user()->jurisdiction->contains('id',$media->ticket->jurisdiction->id) || auth()->user()->hasRole('developer')){
            if ($media->type == 'doc')
                return $this->media('application/pdf', $media);
            else {
                $str = substr($media->path, -4);
                switch (trim($str, '.')) {
                    case 'jpg':
                    case 'jpeg':
                        return $this->media('image/jpeg', $media);
                        break;
                    case 'png':
                        return $this->media('image/png', $media);
                        break;
                    case 'gif':
                        return $this->media('image/gif', $media);
                        break;
                }
            }
        }
    }
    private function media($type, $media){
        return response()->make(file_get_contents(storage_path($media->path)))->header('Content-type',$type)->header('Content-length', filesize(storage_path($media->path)));
    }

}