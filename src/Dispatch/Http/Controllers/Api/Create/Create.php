<?php

namespace Kregel\Dispatch\Http\Controllers\Api\Create;

use Kregel\Dispatch\Http\Controllers\Controller;

class Create extends Controller
{
    public function create($model)
    {
        $models = config('kregel.warden.models');
        if (empty($models)) {
            return response()->json([
                'message' => 'Either the config is missing or misnamed, please fix this and try again',
                'code' => 422,
            ], 422);
        }
        if (!array_key_exists($model, $models)) {
            return response()->json(['message' => 'That model doesn\'t exist!', 'code' => 422], 422);
        }
        $model = $models[$model]['model'];
        $model = new $model();
        $model->fill(request()->all());
        if ($model->save()) {
            return response()->json([
                'message' => 'Your model has been saved!',
                'code' => request()->ajax() ? 202 : 200,
            ], request()->ajax() ? 202 : 200);
        }

        return response()->json([
            'message' => 'We could not save that model.',
            'code' => 422,
        ], 422);
    }
}
