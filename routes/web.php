<?php

use App\Core\Route;

Route::get('/', 'RecipeController@index');

Route::prefix('/recipe')->group(function () {
    Route::get('/', 'RecipeController@index');
    Route::get('/create', 'RecipeController@create');
    Route::post('/create', 'RecipeController@create');
    Route::get('/:id', 'RecipeController@show');
    Route::get('/edit/:id', 'RecipeController@edit');
    Route::post('/edit/:id', 'RecipeController@edit');
    Route::post('/delete/:id', 'RecipeController@delete');
});
