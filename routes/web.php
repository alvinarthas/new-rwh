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
// Route::get('user','UserController@index');
Route::get('/testuser','TestController@index');
Route::get('/user/json','MemberController@json');
Route::get('/','HomeController@index')->name('getHome');
Route::get('/index2','HomeController@index2')->name('getHome2');
Route::post('login','HomeController@login')->name('Login');

Route::middleware(['checkUser'])->group(function () {
    // Absensi
    Route::get('/absensi/register','AbsensiController@register')->name('absensiRegister');
    Route::get('/absensi/kehadiran','AbsensiController@kehadiran')->name('absensiHadir');
    Route::get('/absensi/log','AbsensiController@log')->name('absensiLog');

    // Account
    Route::get('/account/profile','AccountController@profile')->name('showProfile');
    Route::get('/account/change_pass','AccountController@getchange_pass')->name('getChangePass');
    Route::get('/account/change_foto','AccountController@getchange_foto')->name('getChangeFoto');
    Route::post('/account/change_foto','AccountController@change_foto')->name('changeFoto');
    Route::post('/account/change_pass','AccountController@change_pass')->name('changePass');

    // Menu Mapping
    Route::get('/menumapping','MenuController@index')->name('getMapping');
    Route::get('/showmapping/{id}','MenuController@show')->name('showMapping');
    Route::post('/storemapping','MenuController@store')->name('storeMapping');
    Route::post('/deletemapping','MenuController@delete')->name('deleteMapping');

    // Role Mapping
    Route::get('rolemapping','RoleMappingController@index')->name('getRoleMapping');
    Route::get('/rolemapping/{id}/edit','RoleMappingController@edit')->name('editRoleMapping');
    Route::put('/rolemapping/{id}/update','RoleMappingController@update')->name('updateRoleMapping');
    Route::delete('/rolemapping/{id}/delete','RoleMappingController@destroy')->name('destroyRoleMapping');
    Route::get('/manageproduct','ProductController@manage')->name('manageproduct');
    Route::get('/showProdAjx','ProductController@showProdAjx')->name('showProdAjx');
    
    // Resources 
    Route::resources([
        // Employee
        'employee' => 'EmployeeController',
        // Modul Management
        'modul' => 'ModulController',
        // Sub Modul Management
        'submodul' => 'SubModulController',
        // Role Management
        'role' => 'RoleController',
        // Member Management
        'member' => 'MemberController',
        // Perusahaan
        'perusahaan' => 'PerusahaanController',
        // Product
        'product' => 'ProductController',
        // Koordinator
        'koordinator' => 'KoordinatorController',
        // Sub Koordinator
        'subkoordinator' => 'SubkoordinatorController',
        // Manage Harga
        'manageharga' => 'ManageHargaController',
        // Purchasing
        'purchase' => 'PurchaseController',
    ]);

    // Bank Member
        Route::get('/bankmember','BankMemberController@create')->name('createBankMember');
        Route::post('/bankmember/{ktp}','BankMemberController@store')->name('storeBankMember');
        Route::get('/bankmember/edit','BankMemberController@edit')->name('editBankMember');
        Route::put('/bankmember/{ktp}/update/{bid}','BankMemberController@update')->name('updateBankMember');
        Route::get('/bankmember/delete','BankMemberController@destroy')->name('destroyBankMember');
    // Perusahaan Member
        Route::get('perusahaanmember','PerusahaanMemberController@create')->name('createPerusahaanMember');
        Route::post('perusahaanmember/{ktp}','PerusahaanMemberController@store')->name('storePerusahaanMember');
        Route::get('perusahaanmember/edit','PerusahaanMemberController@edit')->name('editPerusahaanMember');
        Route::put('perusahaanmember/{ktp}/update/{pid}','PerusahaanMemberController@update')->name('updatePerusahaanMember');
        Route::get('perusahaanmember/delete','PerusahaanMemberController@destroy')->name('destroyPerusahaanMember');
        
    Route::get('ajxmember','MemberController@ajxmember');
    Route::get('logout','HomeController@logout')->name('Logout');
    // ------------------------ HELPER -------------------------------------------------
    Route::get('/datakota','HelperController@getDataKota')->name('getDataKota');
    // purchase helper
    Route::get('/showpurchase','PurchaseController@showPurchase')->name('showPurchase');
    Route::get('/addpurchase','PurchaseController@addPurchase')->name('addPurchase');
    Route::get('/showindexpurchase','PurchaseController@showIndexPurchase')->name('showIndexPurchase');
    Route::get('/destroydetailpurchase','PurchaseController@destroyPurchaseDetail')->name('destroyPurchaseDetail');

});

// Fingerprint System
Route::get('/finger/register','FingerPrintController@register')->name('fingerRegister');
Route::post('/finger/processregister','FingerPrintController@process_register')->name('fingerProcessRegister');
Route::get('/finger/getac','FingerPrintController@get_ac')->name('fingerGetAc');
Route::get('/finger/message','FingerPrintController@message')->name('fingerMessage');
Route::get('/finger/checkreg','FingerPrintController@checkreg')->name('fingerCheckReg');
Route::get('/finger/verifikasi','FingerPrintController@verification')->name('fingerVerifikasi');
Route::post('/finger/processverifikasi','FingerPrintController@process_verification')->name('fingerProcessVerification');
Route::get('/finger/ajxlog','FingerPrintController@ajxlog')->name('fingerAjxLog');
Route::get('/finger/ajxfulllog','FingerPrintController@ajxfulllog')->name('fingerAjxFullLog');
