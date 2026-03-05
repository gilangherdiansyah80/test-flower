<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::middleware(['web'])->group(function () {
    Route::get('/movies', 'MovieController@index')->name('home');
    Route::get('/movies/{id}', 'MovieController@show')->name('movies.show');
    Route::post('/favorites/toggle', 'MovieController@toggleFavorite')->name('favorites.toggle');
    Route::get('/favorites', 'MovieController@favorites')->name('favorites.list');
    
    Route::get('/lang/{locale}', function ($locale) {
        if (in_array($locale, ['en', 'id'])) {
            session(['locale' => $locale]);
        }
        return back();
    })->name('lang.switch');
});
