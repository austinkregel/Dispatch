<?php

namespace Kregel\Dispatch\Http\Controllers\Api\Upload;

use Illuminate\Http\Request;
use Kregel\Dispatch\Http\Controllers\Controller;

/**
 * Class Upload.
 */
class Upload extends Controller
{
    // This class will handle the Uploads. To centralize any and all
    // Uploads through this controller.


    /**
     * @param Request $r
     *
     * @return simple
     */
    public function storeVideo(Request $r)
    {
        return $this->store($r, [
            'rules' => ['video' => 'required', 'title' => 'required'],
            'not_valid' => ['message' => ['Not a video.'], 'code' => 422],
            'not_saved' => ['message' => ['Error saving the file.'], 'code' => 422],
        ]);
    }
    /**
     * @param Request $r
     *
     * @return simple
     */
    public function storeImage(Request $r)
    {
        return $this->store($r, [
            'rules' => ['image' => 'required|image', 'title' => 'required'],
            'not_valid' => ['message' => ['Not an image.'], 'code' => 422],
            'not_saved' => ['message' => ['Error saving the file.'], 'code' => 422],
        ]);
    }
    /**
     * @param Request $r
     *
     * @return simple
     */
    public function storeDocument(Request $r)
    {
        return $this->store($r, [
            'rules' => ['doc' => 'mimes:txt,pdf,doc,docx', 'title' => 'required'],
            'not_valid' => ['message' => ['Not an image.'], 'code' => 422],
            'not_saved' => ['message' => ['Error saving the file.'], 'code' => 422],
        ]);
    }
}
