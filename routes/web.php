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
// Route::get('user','UserController@index');cache:clear
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

    // Purchase Mapping
    Route::get('purchasemapping','MenuController@PurMapIndex')->name('PurMapIndex');
    Route::get('purchasemapping/{id}','MenuController@PurMapShow')->name('PurMapShow');
    Route::post('purchasemapping/store','MenuController@PurMapStore')->name('PurMapStore');
    Route::post('purchasemapping/delete','MenuController@PurMapDelete')->name('PurMapDelete');

    // Role Mapping
    Route::get('rolemapping','RoleMappingController@index')->name('getRoleMapping');
    Route::get('/rolemapping/{id}/edit','RoleMappingController@edit')->name('editRoleMapping');
    Route::put('/rolemapping/{id}/update','RoleMappingController@update')->name('updateRoleMapping');
    Route::delete('/rolemapping/{id}/delete','RoleMappingController@destroy')->name('destroyRoleMapping');

    Route::get('/manageproduct','ProductController@manage')->name('manageproduct');
    Route::get('/showProdAjx','ProductController@showProdAjx')->name('showProdAjx');

    // Receive Product
    Route::get('receiveproduct','ReceiveProductController@index')->name('receiveProd');
    Route::get('receiveproduct/ajx','ReceiveProductController@ajx')->name('receiveProdAjx');
    Route::get('receiveproduct/detail','ReceiveProductController@detail')->name('receiveProdDet');
    Route::post('receiveproduct/store','ReceiveProductController@store')->name('receiveProdStr');
    Route::get('receiveproduct/delete','ReceiveProductController@delete')->name('receiveProdDel');

    // Bonus
    Route::get('/showBonus', 'BonusController@showBonusPerhitungan')->name('showBonusPerhitungan');
    Route::get('/createBonus', 'BonusController@createBonusPerhitungan')->name('createBonusPerhitungan');
    Route::post('/uploadBonus', 'BonusController@uploadBonusPerhitungan2')->name('uploadBonusPerhitungan');
    Route::post('/ajxaddrowperhitungan', 'BonusController@ajxAddRowPerhitungan')->name('ajxAddRowPerhitungan');

    Route::get('/bonus/bayar','BonusController@indexBayar')->name('bonus.penerimaan');
    Route::get('/bonus/bayar/create','BonusController@createBayar')->name('bonus.createPenerimaan');
    Route::post('/bonus/bayar/create','BonusController@storeBayar')->name('bonus.storePenerimaan');
    Route::get('/showBonusPembayaran', 'BonusController@showBonusPembayaran')->name('showBonusPenerimaan');
    Route::get('/createBonusPembayaran', 'BonusController@createBonusPembayaran')->name('createBonusPenerimaan');
    Route::post('/uploadBonusPenerimaan', 'BonusController@uploadBonusPenerimaan')->name('uploadBonusPenerimaan');
    Route::post('/ajxaddrowpembayaran', 'BonusController@ajxAddRowPembayaran')->name('ajxAddRowPenerimaan');
    Route::post('/exportGagalBonus', 'BonusController@exportGagalBonus')->name('exportGagalBonus');

    Route::get('/bonus/topup','BonusController@indexTopup')->name('bonus.topup');
    Route::get('/bonus/topup/create','BonusController@createTopup')->name('bonus.createtopup');
    Route::post('/bonus/topup/create','BonusController@storeTopup')->name('bonus.storetopup');
    Route::get('/showBonusTopup', 'BonusController@showBonusTopup')->name('showBonusTopup');
    Route::get('/createBonusTopup', 'BonusController@createBonusTopup')->name('createBonusTopup');
    Route::post('/uploadBonusTopup', 'BonusController@uploadBonusTopup')->name('uploadBonusTopup');
    Route::post('/ajxaddrowtopup', 'BonusController@ajxAddRowTopup')->name('ajxAddRowTopup');

    Route::get('/bonus/laporan','BonusController@indexLaporan')->name('bonus.laporan');
    Route::get('/showBonusLaporan', 'BonusController@showLaporanBonus')->name('showLaporanBonus');

    Route::get('/bonus/bonusgagal','BonusController@indexBonusGagal')->name('bonus.bonusgagal');
    Route::get('/showBonusLaporanGagal', 'BonusController@showLaporanBonusGagal')->name('showLaporanBonusGagal');

    Route::get('/ajxbonusorder', 'BonusController@ajxBonusOrder')->name('ajxBonusOrder');
    Route::get('/ajxbonusorderPerhitungan', 'BonusController@ajxBonusOrderPerhitungan')->name('ajxBonusOrderPerhitungan');

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
        // Sales
        'sales' => 'SalesController',
        // Bonus
        'bonus' => 'BonusController',
        // COA
        'coa' => 'CoaController',
        // Jurnal
        'jurnal' => 'JurnalController',
        // Retur
        'retur' => 'ReturController',
        // Customer
        'customer' => 'CustomerController',
        // Saldo
        'saldo' => 'SaldoController',
        // Deposit Pembelian
        'deposit' => 'DepositController',
    ]);

    // Member
        Route::get('ajxmember','MemberController@ajxmember');
        Route::get('ajxMemberOrder', 'MemberController@ajxMemberOrder')->name('ajxMemberOrder');
        Route::get('/ajxaddrowcetak', 'MemberController@ajxAddRowCetak')->name('ajxAddRowCetak');
        Route::post('/exportmember', 'MemberController@exportMember')->name('exportMember');
        Route::get('/synchmember', 'MemberController@makeSynch')->name('createsynchmember');

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

    // INVOICE
        Route::get('invoice','InvoiceController@index')->name('indexInvoice');
        Route::get('invoice/view','InvoiceController@view')->name('invoiceView');
        Route::get('invoice/print','InvoiceController@print')->name('invoicePrint');

    //Retur
        Route::get('/showpembelian', 'ReturController@showReturPembelian')->name('showReturPembelian');
        Route::get('/retur/penjualan/index', 'ReturController@indexpj')->name('retur.indexpj');
        Route::get('/retur/penjualan/create', 'ReturController@createpj')->name('retur.createpj');
        Route::get('/retur/penjualan/edit/{id}', 'ReturController@editpj')->name('retur.editpj');
        Route::put('/retur/penjualan/edit/{id}', 'ReturController@updatepj')->name('retur.updatepj');
        Route::get('/showpenjualan', 'ReturController@showReturPenjualan')->name('showReturPenjualan');

    //Security
        Route::get('/security', 'SecurityController@index')->name('security.index');
        Route::get('/security/getATM', 'SecurityController@getATM')->name('security.getatm');

    // Customer
        Route::get('/deletecustomer/{id}','CustomerController@destroy');
        Route::get('/customer/priceBV/{id}', 'CustomerController@priceBV')->name('customer.pricebv');
        Route::post('/customer/updatepriceBV/{id}', 'CustomerController@updatePriceBV')->name('customer.updatepricebv');
        Route::get('/ajxgetproduct', 'CustomerController@ajxGetProduct')->name('ajxGetProduct');
        Route::get('/ajxaddrowproduct', 'CustomerController@ajxAddRowProduct')->name('ajxAddRowProduct');
        Route::get('/pricedetail/{id}/delete','CustomerController@deletePriceDet');
        Route::get('/pricebycustomer','CustomerController@priceByCustomer')->name('pricebycustomer');
        Route::get('/pricebycustomer/manage/{id}','CustomerController@managePriceByCustomer')->name('managepricebycustomer');
        Route::post('/pricebycustomer/manage/{id}', 'CustomerController@updateManagePriceBV')->name('updatebycustomer');
        Route::get('/cetakXlsProduct/{id}', 'CustomerController@exportProduct')->name('exportXlsProduct');
        Route::get('/cetakXlsCustomer/{id}', 'CustomerController@exportCustomer')->name('exportXlsCustomer');
    
    // Stock Controlling
        Route::get('stockcontrolling','ProductController@controlling')->name('stockControlling');

    // Saldo
        Route::get('ajxCoaOrder', 'SaldoController@ajxCoaOrder')->name('ajxCoaOrder');

    // PAYMENT
        // Sales
        Route::get('salespayment','PaymentController@salesIndex')->name('salesIndex');
        Route::get('salespayment/view','PaymentController@salesView')->name('salesView');
        Route::get('salespayment/{id}/create','PaymentController@salesCreate')->name('salesCreate');
        Route::post('salespayment/store','PaymentController@salesStore')->name('salesStore');
        Route::get('salespayment/destroy','PaymentController@salesPayDestroy')->name('salesPayDestroy');

        // Purchase
        Route::get('purchasepayment','PaymentController@purchaseIndex')->name('purchaseIndex');
        Route::get('purchasepayment/view','PaymentController@purchaseView')->name('purchaseView');
        Route::get('purchasepayment/{id}/create','PaymentController@purchaseCreate')->name('purchaseCreate');
        Route::post('purchasepayment/store','PaymentController@purchaseStore')->name('purchaseStore');
        Route::get('purchasepayment/destroy','PaymentController@purchasePayDestroy')->name('purchasePayDestroy');

    // Delivery Order
        Route::get('do','DeliveryController@index')->name('indexDo');
        Route::get('do/add','DeliveryController@addBrgDo')->name('addBrgDo');
        Route::get('do/view','DeliveryController@view')->name('viewDo');
        Route::get('do/print','DeliveryController@print')->name('printDo');
        Route::get('do/show/{id}','DeliveryController@show')->name('showDo');
        Route::post('do/store','DeliveryController@store')->name('storeDo');
        Route::delete('do/delete','DeliveryController@delete')->name('deleteDo');

    // Laporan
    Route::prefix('laporan')->group(function () {
        // Balance Sheet Neraca Saldo Awal
        Route::get('neracaawal','LaporanController@neraca_awal')->name('neracaAwal');
        // Balance Sheet (Laporan Neraca)
        Route::get('laporannerace','LaporanController@laporan_neraca')->name('neracaLaporan');
        Route::get('viewlapnerace','LaporanController@laporan_neraca_view')->name('neracaLaporanView');
        // General Ledger
        Route::get('generalledger','LaporanController@index_gl')->name('indexGL');
        Route::get('viewgl','LaporanController@view_gl')->name('viewGL');
        Route::get('viewgljurnal','LaporanController@view_glJurnal')->name('viewGlJurnal');
        // Perubahan Modal
        Route::get('perubahanmodal','LaporanController@perubahanModal')->name('perubahanModal');
        // Profit Loss
        Route::get('profitloss','LaporanController@profitLoss')->name('profitLoss');
    });

    Route::get('logout','HomeController@logout')->name('Logout');

    // Salary
    Route::prefix('salary')->group(function () {
        // Record Poin
        Route::get('poin','SalaryController@indexPoin')->name('indexPoin');
        Route::get('poin/detail','SalaryController@detailPoin')->name('detailPoin');
        Route::get('poin/form','SalaryController@formPoin')->name('formPoin');
        Route::post('poin/store','SalaryController@storePoin')->name('storePoin');
        Route::delete('poin/delete','SalaryController@delPoin')->name('delPoin');

        // Perhitungan Gaji
        Route::get('perhitungan','SalaryController@indexPerhitunganGaji')->name('indexPerhitunganGaji');
        Route::get('perhitungan/detail','SalaryController@detGajiPegawai')->name('detGajiPegawai');
        Route::get('perhitungan/create','SalaryController@createPerhitunganGaji')->name('createPerhitunganGaji');
        Route::post('perhitungan/store','SalaryController@storePerhitunganGaji')->name('storePerhitunganGaji');
        Route::delete('perhitungan/delete','SalaryController@deletePerhitunganGaji')->name('deletePerhitunganGaji');
    });

    // ------------------------ HELPER -------------------------------------------------
    Route::get('/datakota','HelperController@getDataKota')->name('getDataKota');
    Route::get('/datacoa','HelperController@ajxCoa')->name('ajxCoa');

    // purchase helper
    Route::get('/showpurchase','PurchaseController@showPurchase')->name('showPurchase');
    Route::get('/addpurchase','PurchaseController@addPurchase')->name('addPurchase');
    Route::get('/showindexpurchase','PurchaseController@showIndexPurchase')->name('showIndexPurchase');
    Route::get('/destroydetailpurchase','PurchaseController@destroyPurchaseDetail')->name('destroyPurchaseDetail');

    // sales helper
    Route::get('/showsales','SalesController@showSales')->name('showSales');
    Route::get('/addsales','SalesController@addSales')->name('addSales');
    Route::get('/showindexsales','SalesController@showIndexSales')->name('showIndexSales');
    Route::get('/destroydetailsales','SalesController@destroySalesDetail')->name('destroySalesDetail');

    // Saldo Helper
    Route::get('checksaldo','HelperController@checkSaldo')->name('checkSaldo');
    Route::get('checkdeposit','HelperController@checkDeposit')->name('checkDeposit');

    // Jurnal Helper
    Route::get('jurnaladd','JurnalController@addJurnal')->name('addJurnal');
    Route::get('jurnaldetaildestroy','JurnalController@detailJuralDestroy')->name('detailJuralDestroy');

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
// Purchase Approve
Route::get('/finger/purchaseapprove','FingerPrintController@purchaseApprove')->name('purchaseApprove');
Route::post('/finger/purchaseapproveprocess','FingerPrintController@purchaseApproveProcess')->name('purchaseApproveProcess');
// Sales Approve
Route::get('/finger/salesapprove','FingerPrintController@salesApprove')->name('salesApprove');
Route::post('/finger/salesapproveprocess','FingerPrintController@salesApproveProcess')->name('salesApproveProcess');
