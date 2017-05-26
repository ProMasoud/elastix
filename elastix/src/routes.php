<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
Route::get('prueba', 'Selkis\Elastix\Controllers\ControllerTelephonePanel@prueba');
Route::get('barra', 'Selkis\Elastix\Controllers\ControllerTelephonePanel@getVista');
Route::get('status', 'Selkis\Elastix\Controllers\ControllerTelephonePanel@getIndex');
Route::post('agentlogin', 'Selkis\Elastix\Controllers\ControllerTelephonePanel@agentlogin');
Route::post('agentlogout', 'Selkis\Elastix\Controllers\ControllerTelephonePanel@agentlogout');
Route::post('abreak', 'Selkis\Elastix\Controllers\ControllerTelephonePanel@abreak');
Route::post('unbreak', 'Selkis\Elastix\Controllers\ControllerTelephonePanel@unbreak');
Route::post('hold', 'Selkis\Elastix\Controllers\ControllerTelephonePanel@hold');
Route::post('unhold', 'Selkis\Elastix\Controllers\ControllerTelephonePanel@unhold');
Route::post('hangup', 'Selkis\Elastix\Controllers\ControllerTelephonePanel@hangup');
Route::post('colgar', 'Selkis\Elastix\Controllers\ControllerTelephonePanel@colgar');
Route::post('call', 'Selkis\Elastix\Controllers\ControllerTelephonePanel@call');
Route::post('transfer', 'Selkis\Elastix\Controllers\ControllerTelephonePanel@transfer');
