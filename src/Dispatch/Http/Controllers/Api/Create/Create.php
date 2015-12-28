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
                'code' => 442,
            ], 422);
        }
        if (!array_key_exists($model, $models)) {
            return response()->json(['message' => 'That model doesn\'t exist!', 'code' => 422], 422);
        }
        $model = $models[$model]['model'];

        return $model::create(request()->all());
    }
}
