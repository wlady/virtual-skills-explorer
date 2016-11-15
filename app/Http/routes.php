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

Route::get('/', 'WelcomeController@index');
Route::post('filter', 'SearchController@filter');
//Route::get('home', 'HomeController@index');
//Route::get('search', 'SearchController@search');
//Route::get('search/count', 'SearchController@count');
//Route::get('search/skills', 'SearchController@skills');

//Route::controllers([
//	'auth' => 'Auth\AuthController',
//	'password' => 'Auth\PasswordController',
//]);
