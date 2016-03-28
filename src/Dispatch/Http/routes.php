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
    Route::group(['as' => 'new.', 'prefix' => 'new'], function () {
        Route::get('ticket', ['as' => 'ticket', 'uses' => 'TicketsController@create']);
        Route::get('ticket/{buss_name}', ['as' => 'jurisdiction-ticket', 'uses' => 'TicketsController@create']);
        Route::post('ticket-photo/{id}', ['as' => 'ticket-photo', 'uses' => 'TicketsController@postTicketCreate']);
    });
    Route::group(['as' => 'view.'], function () {
        Route::get('tickets', ['as' => 'tickets', 'uses' => 'TicketsController@viewAll']);
        Route::get('ticket/{name}', ['as' => 'ticket', 'uses' => 'TicketsController@getTicketsForJurisdiction']);
        Route::get('ticket/{name}/{id}', ['as' => 'ticket-single', 'uses' => 'TicketsController@getTicketFromJurisdiction']);
        Route::get('jurisdictions', ['as' => 'jurisdiction', 'uses' => 'JurisdictionController@viewAll']);

        Route::group(['as' => 'closed.'], function(){
            Route::get('closed/{name}', ['as' => 'tickets', 'uses' => 'TicketsController@getClosedTicketsFromJurisdiction']);

        });

        Route::get('media/{uuid}', ['as' => 'media', 'uses' => 'MediaController@showMedia']);
    });

    Route::group(['as' => 'edit.'], function () {
        Route::get('ticket/{name}/{id}/edit', ['as' => 'ticket', 'uses' => 'TicketsController@getTicketFromJurisdictionForEdit']);
        Route::get('jurisdiction/{place?}', ['as' => 'jurisdiction', 'uses' => 'JurisdictionController@getJurisdictionForEdit']);

    });
    Route::group(['as' => 'profile.', 'prefix' => config('kregel.auth-login.profile.route')], function () {
        Route::get('{id}/{name?}', ['as' => 'user', 'uses' => 'ProfileController@viewProfile']);
    });
});
