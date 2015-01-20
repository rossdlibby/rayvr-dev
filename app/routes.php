<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', function()
{
	return View::make('hello');
});

Route::get('register', array(
	'uses' => 'RegisterController@index',
	'as' => 'register.index'
));

Route::post('register', array(
	'uses' => 'RegisterController@store',
	'as' => 'register.store'
));

/**
 * Preference route
 */
Route::resource('preferences', 'PreferencesController');

/**
 * Session routes
 */
Route::get('login', array(
	'uses' => 'SessionController@create',
	'as' => 'session.create'
));
Route::post('login', array(
	'uses' => 'SessionController@store',
	'as' => 'session.store'
));
Route::get('logout', array(
	'uses' => 'SessionController@destroy',
	'as' => 'session.destroy'
));