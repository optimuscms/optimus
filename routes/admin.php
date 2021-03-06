<?php

// Auth
Route::get('login', 'Auth\LoginController@showLoginForm');
Route::post('login', 'Auth\LoginController@login')->name('admin.login');
Route::post('logout', 'Auth\LoginController@logout')->name('admin.logout');

// Api
Route::prefix('api')->middleware('auth:admin')->namespace('Api')->group(function () {
    // Admin Users
    Route::prefix('admin-users')->group(function () {
        Route::get('/', 'AdminUsersController@index');
        Route::post('/', 'AdminUsersController@store');
        Route::get('{id}', 'AdminUsersController@show');
        Route::patch('{id}', 'AdminUsersController@update');
        Route::delete('{id}', 'AdminUsersController@destroy');
    });

    // Current Admin User
    Route::get('admin-user', 'AdminUsersController@show');

    // Media Folders
    Route::prefix('media-folders')->group(function () {
        Route::get('/', 'MediaFoldersController@index');
        Route::post('/', 'MediaFoldersController@store');
        Route::get('{id}', 'MediaFoldersController@show');
        Route::patch('{id}', 'MediaFoldersController@update');
        Route::delete('{id}', 'MediaFoldersController@destroy');
    });

    // Media
    Route::prefix('media')->group(function () {
        Route::get('/', 'MediaController@index');
        Route::post('/', 'MediaController@store');
        Route::get('{id}', 'MediaController@show');
        Route::patch('{id}', 'MediaController@update');
        Route::delete('{id}', 'MediaController@destroy');
    });

    // Page Templates
    Route::prefix('page-templates')->group(function () {
        Route::get('/', 'PageTemplatesController@index');
        Route::get('{id}', 'PageTemplatesController@show');
    });

    // Pages
    Route::prefix('pages')->group(function () {
        Route::get('/', 'PagesController@index');
        Route::post('/', 'PagesController@store');
        Route::get('{id}', 'PagesController@show');
        Route::patch('{id}', 'PagesController@update');
        Route::delete('{id}', 'PagesController@destroy');
    });

    // Linkables
    Route::get('linkable-types', 'LinkableTypesController@index');
    Route::get('linkable-types/{alias}', 'LinkableTypesController@show');
    Route::get('linkable-types/{alias}/items', 'LinkableItemsController@index');

    // Menus
    Route::get('menus', 'MenusController@index');
    Route::post('menus', 'MenusController@store');
    Route::get('menus/{id}', 'MenusController@show');
    Route::patch('menus/{id}', 'MenusController@update');
    Route::delete('menus/{id}', 'MenusController@destroy');

    // Menu Items
    Route::get('menus/{menuId}/items', 'MenuItemsController@index');
    Route::post('menus/{menuId}/items', 'MenuItemsController@store');
    Route::get('menu-items/{id}', 'MenuItemsController@show');
    Route::patch('menu-items/{id}', 'MenuItemsController@update');
    Route::put('menu-items/{id}/move', 'MenuItemsController@move');
    Route::delete('menu-items/{id}', 'MenuItemsController@destroy');

    /*--OPTIMUS-CLI:routes--*/
});

Route::view('{path?}', 'back.layouts.app')
     ->middleware('auth:admin')
     ->where('path', '.*')
     ->name('admin.dashboard');
