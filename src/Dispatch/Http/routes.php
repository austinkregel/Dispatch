<?php
/**
 */
Route::group(['prefix' => config('kregel.dispatch.route'), 'as' => 'dispatch::', 'middleware' => 'auth'], function () {

    /**
     * Api Routes for Dispatch.
     */
    require 'apiroutes.php';

    Route::get('/', function () {
        return view('dispatch::home');
    });
    Route::group(['as' => 'new.'], function () {
        Route::get('ticket', ['as' => 'ticket', 'uses' => 'TicketsController@create']);
        Route::get('jurisdiction/{place?}', ['as' => 'jurisdiction', 'uses' => 'JurisdictionController@create']);
    });
    Route::group(['as' => 'view.'], function () {
        Route::get('tickets', ['as' => 'ticket', 'uses' => 'TicketsController@viewAll']);
        Route::get('jurisdictions', ['as' => 'jurisdiction', 'uses' => 'JurisdictionController@viewAll']);
    });
});
