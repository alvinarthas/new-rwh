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

Route::get('/','HomeController@index')->name('getHome');
Route::post('login','HomeController@login')->name('Login');

Route::middleware(['checkUser'])->group(function () {

    // Employee Management
    Route::get('/employee','EmployeeController@index')->name('getEmployee');
    Route::get('/employee/create','EmployeeController@create')->name('createEmployee');
    Route::post('/employee/store','EmployeeController@store')->name('storeEmployee');
    Route::get('/employee/{id}/edit','EmployeeController@edit')->name('editEmployee');
    Route::put('/employee/{id}/update','EmployeeController@update')->name('updateEmployee');
    Route::delete('/employee/{id}/delete','EmployeeController@update')->name('destroyEmployee');

    // Menu Mapping
    Route::get('/menumapping','MenuController@index')->name('getMapping');
    Route::get('/showmapping/{id}','MenuController@show')->name('showMapping');
    Route::post('/storemapping','MenuController@store')->name('storeMapping');
    Route::post('/deletemapping','MenuController@delete')->name('deleteMapping');

    Route::resources([
        // Modul Management
        'modul' => 'ModulController',
        // Sub Modul Management
        'submodul' => 'SubModulController',
    ]);

});

