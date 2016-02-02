<?php

Route::group(['prefix' => 'api/v1.0'], function () {

/*
 * This is the group for uploading
 * @return void
 */
Route::group(['prefix' => 'upload'], function () {
    Route::post('document', ['as' => 'upload.document', 'uses' => 'Api\Upload\Upload@storeDocument']);
    Route::post('image', ['as' => 'upload.document', 'uses' => 'Api\Upload\Upload@storeImage']);
    Route::post('video', ['as' => 'upload.document', 'uses' => 'Api\Upload\Upload@storeVideo']);
});
/*
 * This is the group for creating objects
 * @return void
 */
Route::group(['prefix' => 'create'], function () {
//            Route::post('document', '');
//            Route::post('image', '');
//            Route::post('video', '');
    Route::post('{model}', 'Api\Create\Create@create'); // I'm throwing in ALLLL the creates :3
});

/*
 * This a group for updating (put requests)
 * @return void
 */
Route::group(['prefix' => 'update'], function () {
//            Route::put('document/{uuid}', '');
//            Route::put('image/{uuid}', '');
//            Route::put('video/{uuid}', '');
//            Route::put('{models}/{uuid}', '');
});

/*
 * This a group for deleting (delete requests)
 * @return void
 */
Route::group(['prefix' => 'delete'], function () {
//            Route::delete('document/{uuid}', '');
//            Route::delete('image/{uuid}', '');
//            Route::delete('video/{uuid}', '');
//            Route::delete('{models}/{uuid}', '');
});

/*
 * This is the default retrieval for various models
 */
// This should pull the document from the proper storage_path() and set the headers
//        Route::get('document/{uuid}', '');
//        // This should pull the image from the proper storage_path() and set the headers
//        Route::get('image/{uuid}', '');
//        // This should pull the video from the proper storage_path() and set the headers
//        Route::get('video/{uuid}', '');
//        Route::post('{models}/{uuid}', '');
});
