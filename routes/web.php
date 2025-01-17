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

use App\Http\Controllers\JurnalController;

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
    Route::post('receiveproduct/ajx','ReceiveProductController@ajx')->name('receiveProdAjx');
    Route::get('receiveproduct/add','ReceiveProductController@addBrgReceive')->name('addBrgReceive');
    Route::get('receiveproduct/view','ReceiveProductController@view')->name('viewRI');
    Route::get('receiveproduct/detail/{id}','ReceiveProductController@detail')->name('receiveProdDet');
    Route::post('receiveproduct/store','ReceiveProductController@store')->name('receiveProdStr');
    Route::get('receiveproduct/edit/{id}','ReceiveProductController@edit')->name('receiveProdEdit');
    Route::post('receiveproduct/addProduct','ReceiveProductController@addProduct')->name('receiveProdAddProd');
    Route::put('receiveproduct/{id}/update','ReceiveProductController@update')->name('receiveProdUpd');
    Route::delete('receiveproduct/delete','ReceiveProductController@delete')->name('receiveProdDel');
    Route::delete('receiveproduct/deleteProd','ReceiveProductController@deleteProd')->name('receiveProdDelProd');

    // Bonus
    Route::get('/showBonus', 'BonusController@showBonusPerhitungan')->name('showBonusPerhitungan');
    Route::get('/createBonus', 'BonusController@createBonusPerhitungan')->name('createBonusPerhitungan');
    Route::post('/uploadBonus', 'BonusController@uploadBonusPerhitungan2')->name('uploadBonusPerhitungan');
    Route::post('/ajxaddrowperhitungan', 'BonusController@ajxAddRowPerhitungan')->name('ajxAddRowPerhitungan');
    Route::get('/perhitunganbonus/{id}/deleterow','BonusController@deleteRowPerhitungan');

    Route::get('/bonus/bayar','BonusController@indexBayar')->name('bonus.penerimaan');
    Route::get('/bonus/bayar/create','BonusController@createBayar')->name('bonus.createPenerimaan');
    Route::post('/bonus/bayar/create','BonusController@storeBayar')->name('bonus.storePenerimaan');
    Route::get('/bonus/bayar/{id}/edit','BonusController@editBayar')->name('bonus.editPenerimaan');
    Route::put('/bonus/bayar/{id}/edit','BonusController@updateBayar')->name('bonus.updatePenerimaan');
    Route::delete('/bonus/bayar/{id}/delete', 'BonusController@destroyBayar')->name('bonus.deletePenerimaan');
    Route::get('/showBonusPembayaran', 'BonusController@showBonusPembayaran')->name('showBonusPenerimaan');
    Route::get('/createBonusPembayaran', 'BonusController@createBonusPembayaran')->name('createBonusPenerimaan');
    Route::post('/uploadBonusPenerimaan', 'BonusController@uploadBonusPenerimaan')->name('uploadBonusPenerimaan');
    Route::post('/ajxaddrowpembayaran', 'BonusController@ajxAddRowPembayaran')->name('ajxAddRowPenerimaan');
    Route::post('/exportGagalBonus', 'BonusController@exportGagalBonus')->name('exportGagalBonus');
    Route::get('/penerimaanbonus/{id}/deleterow','BonusController@deleteRowPenerimaan');

    Route::get('/bonus/topup','BonusController@indexTopup')->name('bonus.topup');
    Route::get('/bonus/topup/create','BonusController@createTopup')->name('bonus.createtopup');
    Route::post('/bonus/topup/create','BonusController@storeTopup')->name('bonus.storetopup');
    Route::get('/bonus/topup/{id}/edit','BonusController@editTopup')->name('bonus.edittopup');
    Route::put('/bonus/topup/{id}/edit','BonusController@updateTopup')->name('bonus.updatetopup');
    Route::delete('/bonus/topup/delete', 'BonusController@destroyTopup')->name('bonus.deletetopup');
    Route::get('/showBonusTopup', 'BonusController@showBonusTopup')->name('showBonusTopup');
    Route::get('/createBonusTopup', 'BonusController@createBonusTopup')->name('createBonusTopup');
    Route::post('/uploadBonusTopup', 'BonusController@uploadBonusTopup')->name('uploadBonusTopup');
    Route::post('/ajxaddrowtopup', 'BonusController@ajxAddRowTopup')->name('ajxAddRowTopup');
    Route::get('/topupbonus/{id}/deleterow','BonusController@deleteRowTopup');
    Route::get('/checkEstimasiBonus', 'BonusController@checkEstimasiBonus')->name('checkEstimasiBonus');

    Route::get('/bonus/laporan','BonusController@indexLaporan')->name('bonus.laporan');
    Route::post('/showBonusLaporan', 'BonusController@showLaporanBonus')->name('showLaporanBonus');

    Route::get('/bonus/estimasi', 'BonusController@indexEstimasi')->name('bonus.estimasi');
    Route::get('/showEstimasiBonus', 'BonusController@showEstimasiBonus')->name('showEstimasiBonus');

    Route::get('/bonus/bonusgagal','BonusController@indexBonusGagal')->name('bonus.bonusgagal');
    Route::delete('/bonus/bonusgagal/{id}/delete', 'BonusController@destroyGagalBonus')->name('bonus.deletegagalbonus');
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
        'returbeli' => 'ReturPembelianController',
        // Retur
        'returjual' => 'ReturPenjualanController',
        // Customer
        'customer' => 'CustomerController',
        // Saldo
        'saldo' => 'SaldoController',
        // Deposit Pembelian
        'deposit' => 'DepositController',
        // Task
        'task' => 'TaskController',
        // Konversi
        'konversi' => 'KonversiController',
        // Ecommerce
        'ecommerce' => 'EcommerceController',
        // Piutang
        'piutang' => 'PiutangController',
        // Gudang
        'gudang' => 'GudangController',
        // Konversi
        'transit' => 'TransitProductController',
    ]);

    // Member
        Route::get('ajxmember','MemberController@ajxmember');
        Route::get('ajxMemberOrder', 'MemberController@ajxMemberOrder')->name('ajxMemberOrder');
        Route::get('/ajxaddrowcetak', 'MemberController@ajxAddRowCetak')->name('ajxAddRowCetak');
        Route::post('/exportmember', 'MemberController@exportMember')->name('exportMember');
        Route::get('/synchmember', 'MemberController@makeSynch')->name('createsynchmember');
        Route::get('/index_new', 'MemberController@index_new')->name('indexMemberNew');
        Route::post('/getDataMember', 'MemberController@getDataMember')->name('getDataMember');


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

    // Retur Pembelian
        Route::get('/showpembelian', 'ReturPembelianController@showReturPembelian')->name('showReturPembelian');
        Route::get('/returbeli/payment/index', 'ReturPembelianController@indexReturPayment')->name('returbeli.indexpayment');
        Route::get('/returbeli/payment/edit/{id}', 'ReturPembelianController@editReturPayment')->name('returbeli.editpayment');
        Route::post('/returbeli/payment/edit/{id}', 'ReturPembelianController@updateReturPayment')->name('returbeli.updatepayment');
        Route::get('/returbeli/payment/destroy','ReturPembelianController@destroyPayment')->name('returbeli.destroypayment');
        Route::get('/returbeli/receive/index', 'ReturPembelianController@indexReturReceive')->name('returbeli.indexreceive');
        Route::get('/returbeli/receive/edit/{id}', 'ReturPembelianController@editReturReceive')->name('returbeli.editreceive');
        Route::post('/returbeli/receive/edit/{id}', 'ReturPembelianController@updateReturReceive')->name('returbeli.updatereceive');
        Route::delete('/returbeli/receive/delete','ReturPembelianController@destroyReturReceive')->name('returbeli.destroyreceive');
        Route::get('/returbeli/receive/show','ReturPembelianController@showReturReceive')->name('showReturReceive');
        Route::get('returbeli/add','ReturPembelianController@addBrgRetur')->name('addBrgRetur');

    // Retur Penjualan
        Route::get('/showpenjualan', 'ReturPenjualanController@showReturPenjualan')->name('showReturPenjualan');
        Route::get('/returjual/payment/index', 'ReturPenjualanController@indexReturPayment')->name('returjual.indexpayment');
        Route::get('/returjual/payment/edit/{id}', 'ReturPenjualanController@editReturPayment')->name('returjual.editpayment');
        Route::post('/returjual/payment/edit/{id}', 'ReturPenjualanController@updateReturPayment')->name('returjual.updatepayment');
        Route::get('/returjual/payment/destroy','ReturPenjualanController@destroyPayment')->name('returjual.destroypayment');
        Route::get('/returjual/delivery/index', 'ReturPenjualanController@indexReturDelivery')->name('returjual.indexdelivery');
        Route::get('/returjual/delivery/edit/{id}', 'ReturPenjualanController@editReturDelivery')->name('returjual.editdelivery');
        Route::post('/returjual/delivery/edit/{id}', 'ReturPenjualanController@updateReturDelivery')->name('returjual.updatedelivery');
        Route::get('/returjual/delivery/destroy','ReturPenjualanController@destroyReturDelivery')->name('returjual.destroydelivery');
        Route::get('/returjual/delivery/show', 'ReturPenjualanController@showReturDelivery')->name('showReturDelivery');

    // Security
        Route::get('/security', 'SecurityController@index')->name('security.index');
        Route::get('/security/getATM', 'SecurityController@getATM')->name('security.getatm');

    // Customer
        Route::get('/deletecustomer/{id}','CustomerController@destroy');
        Route::get('/pricecustomer', 'CustomerController@priceCustomer')->name('priceCustomer');
        Route::get('/customer/priceBV/{id}', 'CustomerController@priceBV')->name('customer.pricebv');
        Route::post('/customer/updatepriceBV/{id}', 'CustomerController@updatePriceBV')->name('customer.updatepricebv');
        Route::get('/ajxgetproduct', 'CustomerController@ajxGetProduct')->name('ajxGetProduct');
        Route::get('/ajxaddrowproduct', 'CustomerController@ajxAddRowProduct')->name('ajxAddRowProduct');
        Route::get('/pricedetail/{id}/delete','CustomerController@deletePriceDet');
        Route::get('/pricebyproduct','CustomerController@priceByProduct')->name('priceByProduct');
        Route::get('/pricebyproduct/manage/{id}','CustomerController@managePriceByProduct')->name('managePriceByProduct');
        Route::post('/pricebyproduct/manage/{id}', 'CustomerController@updateManagePriceProduct')->name('updateManagePriceProduct');
        Route::get('/cetakXlsProduct/{id}', 'CustomerController@exportProduct')->name('exportXlsProduct');
        Route::get('/cetakXlsCustomer/{id}', 'CustomerController@exportCustomer')->name('exportXlsCustomer');
        Route::get('/customerstock', 'ProductController@customerStock')->name('customerStock');
        Route::get('getCustomer', 'CustomerController@getCustomer')->name('getCustomer');

    // Stock Controlling
        Route::get('/stockcontrolling','ProductController@controlling')->name('stockControlling');
        Route::get('/stockcontrolling/{id}/mutasi/brgindent', 'ProductController@mutasiBrgIndent');
        Route::get('/stockcontrolling/{id}/mutasi/brggudang', 'ProductController@mutasiBrgGudang');
        Route::get('/stockcontrolling/{id}/mutasi/brgcustomer', 'ProductController@mutasiBrgCustomer');
        Route::post('/stockcontrolling/export/', 'ProductController@export')->name('exportStock');

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
        Route::post('getDataDO','DeliveryController@getDataDO')->name('getDataDO');
        Route::post('getDataSO','DeliveryController@getDataSO')->name('getDataSO');
        Route::get('do/add','DeliveryController@addBrgDo')->name('addBrgDo');
        Route::get('do/view','DeliveryController@view')->name('viewDo');
        Route::get('do/print','DeliveryController@print')->name('printDo');
        Route::get('do/show/{id}','DeliveryController@show')->name('showDo');
        Route::post('do/store','DeliveryController@store')->name('storeDo');
        Route::post('do/addProduct','DeliveryController@addProduct')->name('doAddProd');
        Route::get('do/edit/{id}','DeliveryController@edit')->name('editDo');
        Route::put('do/update/{id}','DeliveryController@update')->name('doUpdate');
        Route::delete('do/delete','DeliveryController@delete')->name('deleteDo');
        Route::delete('do/deleteProd','DeliveryController@deleteProd')->name('doDelProd');

    // Laporan
    Route::prefix('laporan')->group(function () {
        // Balance Sheet Neraca Saldo Awal
        Route::get('neraca','LaporanController@neraca')->name('neraca');
        // General Ledger
        Route::get('generalledger','LaporanController@generalLedger')->name('generalLedger');
        Route::post('getDataGeneralLedger', 'LaporanController@getDataGeneralLedger')->name('getDataGeneralLedger');
        // Perubahan Modal
        Route::get('perubahanmodal','LaporanController@perubahanModal')->name('perubahanModal');
        // Profit Loss
        Route::get('profitloss','LaporanController@profitLoss')->name('profitLoss');
        // Purchase
        Route::get('purchase','LaporanController@purchaseReport')->name('purchaseReport');
        // Sales
        Route::get('sales','LaporanController@salesReport')->name('salesReport');
        // Laporan Keuangan
        Route::get('finance','LaporanController@financeReport')->name('financeReport');
        // Laporan Sisa Hutang
        Route::get('sisahutang','LaporanController@sisaHutangReport')->name('sisaHutangReport');
        // Laporan Sisa Piutang
        Route::get('sisapiutang','LaporanController@sisaPiutangReport')->name('sisaPiutangReport');
        // Laporan Kendali Barang
        Route::get('kendalibarang','LaporanController@kendaliBarang')->name('kendaliBarang');
        // Laporan Gross Profit
        Route::get('checkgrossprofit','LaporanController@checkGrossProfit')->name('checkGrossProfit');
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
        Route::post('perhitungan/saveaspdf', 'SalaryController@saveAsPdf')->name('saveAsPdf');
        Route::post('perhitungan/exportGaji', 'SalaryController@export')->name('exportGaji');

    });

    // ------------------------ HELPER -------------------------------------------------
    Route::get('/datakota','HelperController@getDataKota')->name('getDataKota');
    Route::get('/datacoa','HelperController@ajxCoa')->name('ajxCoa');
    Route::get('/recycleso','HelperController@recycleSO')->name('recycleSO');
    Route::get('/recyclepo','HelperController@recyclePO')->name('recyclePO');
    Route::get('/inbalancejurnal','HelperController@inBalanceJurnal')->name('inBalanceJurnal');
    Route::get('/inbalancedo','HelperController@inBalanceDO')->name('inBalanceDO');
    Route::get('coatable','CoaController@coaTable')->name('coaTable');
    Route::get('supplier', 'PerusahaanController@getSupplier')->name('getSupplier');

    // purchase helper
    Route::get('/showpurchase','PurchaseController@showPurchase')->name('showPurchase');
    Route::get('/addpurchase','PurchaseController@addPurchase')->name('addPurchase');
    Route::post('purchaseData','PurchaseController@purchaseData')->name('purchaseData');
    Route::get('/showindexpurchase','PurchaseController@showIndexPurchase')->name('showIndexPurchase');
    Route::get('/destroydetailpurchase','PurchaseController@destroyPurchaseDetail')->name('destroyPurchaseDetail');
    Route::post('/purchase/export/', 'PurchaseController@export')->name('exportPO');

    // sales helper
    Route::get('/showsales','SalesController@showSales')->name('showSales');
    Route::get('/customersales','SalesController@customerSales')->name('customerSales');
    Route::post('salesData','SalesController@salesData')->name('salesData');
    Route::get('/addsales','SalesController@addSales')->name('addSales');
    Route::get('/showindexsales','SalesController@showIndexSales')->name('showIndexSales');
    Route::get('/destroydetailsales','SalesController@destroySalesDetail')->name('destroySalesDetail');
    Route::post('/sales/export/', 'SalesController@export')->name('exportSO');

    // Saldo Helper
    Route::get('checksaldo','HelperController@checkSaldo')->name('checkSaldo');
    Route::get('checkdeposit','HelperController@checkDeposit')->name('checkDeposit');

    // Jurnal Helper
    Route::get('jurnalDraft', 'JurnalController@indexDraft')->name('jurnalDraft');
    Route::get('jurnaladd','JurnalController@addJurnal')->name('addJurnal');
    Route::post('getDataJurnal', 'JurnalController@getData')->name('getDataJurnal');
    Route::get('/jurnal_draft/index', 'JurnalController_draft@index')->name('jurnal_draft.index');
    Route::get('jurnal_draftshow2', 'JurnalController_draft@show2')->name('jurnal_draft.show2');
    Route::get('jurnaldetaildestroy','JurnalController@detailJuralDestroy')->name('detailJuralDestroy');

    // Bonus Helper
    // Route::post('repairEstimasibonus', 'BonusController@RepairEstimasi')->name('repairEstimasiBonus');
    Route::post('/bonus/export', 'BonusController@export')->name('exportBonus');

    // Task Helper
    Route::put('/task/{id}/updateStatus','TaskController@statusUpdate')->name('task.updatestatus');
    Route::put('/task/{id}/updateRead','TaskController@readUpdate')->name('task.updateread');
    Route::post('/ajxaddtaskcomment', 'TaskController@ajxAddTaskComment')->name('ajxAddTaskComment');
    Route::put('/task/{id}/done','TaskController@taskDone')->name('task.done');
    Route::delete('task/image/{id}', 'TaskController@deleteImage');

    // Konversi Helper
    Route::get('/addkonversi','KonversiController@addKonversi')->name('addKonversi');
    Route::get('/destroydetailkonversi','KonversiController@destroyKonversiDetail')->name('destroyKonversiDetail');

    // Transit Helper
    Route::get('/availablegudang','TransitProductController@getAvailableGudang')->name('getAvailableGudang');
    Route::get('/totalgudang','TransitProductController@getGudangTotal')->name('getGudangTotal');
    Route::get('/destroydetailtransit','TransitProductController@destroyTransitDetail')->name('destroyTransitDetail');
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
