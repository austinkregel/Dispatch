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
        Route::get('jurisdiction/{place?}', ['as' => 'jurisdiction', 'uses' => 'JurisdictionController@create']);
    });
    Route::group(['as' => 'view.'], function () {
        Route::get('tickets', ['as' => 'tickets', 'uses' => 'TicketsController@viewAll']);
        Route::get('ticket/{name}', ['as' => 'ticket', 'uses' => 'TicketsController@getTicketsForJurisdiction']);
        Route::get('ticket/{name}/{id}', ['as' => 'ticket-single', 'uses' => 'TicketsController@getTicketFromJurisdiction']);
        Route::get('ticket/{name}/{id}/edit', ['as' => 'edit-ticket', 'uses' => 'TicketsController@getTicketFromJurisdictionForEdit']);

        Route::get('jurisdictions', ['as' => 'jurisdiction', 'uses' => 'JurisdictionController@viewAll']);
    });
    Route::group(['as' => 'profile.', 'prefix' => config('kregel.auth-login.profile.route')], function () {
        Route::get('{id}/{name?}', ['as' => 'user', 'uses' => 'ProfileController@viewProfile']);
    });
});
