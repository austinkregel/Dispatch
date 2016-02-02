<?php

namespace Kregel\Dispatch\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Validator;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @param Request $r
     * @param array   $data
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $r, array $valid)
    {
        $status = ($r->ajax() ? 202 : 200);
        $file = $r->file('file');
        $validator = Validator::make($r->all(), $valid['rules']); // Make sure that the file conforms to the rules.
        if ($validator->fails()) {
            $valid['not_valid']['message'] = $validator->getMessageBag()->toArray();

            return response()->json($valid['not_valid'], 422);
        }
        $destinationPath = storage_path().'/app/uploads/';
        if (empty($file)) {
            return response()->json($valid['not_saved'], 422);
        } elseif (!$file->move($destinationPath, $file->getClientOriginalName())) {
            $valid['not_saved']['message'] = $file->getErrorMessage();

            return response()->json($valid['not_saved'], 422);
        }

        return response()->json(['success' => true, 'code' => $status], $status);
    }
}
