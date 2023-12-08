<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

// Cashfree Payment
Route::get('cashfree/initiate-payment','CashfreeController@initiate')->name('initiate');
Route::any('cashfree/success-payment','CashfreeController@successPayment')->name('success-payment');

Route::get('cashfree/refund-payment','CashfreeController@refundPayment')->name('refund-payment');


// PhonePe Payment
Route::get('phonepe/initiate-payment','PhonepeController@initiate')->name('initiate');
Route::any('phonepe/success-payment','PhonepeController@successPayment')->name('success-payment');

Route::get('phonepe/refund-payment','PhonepeController@refundPayment')->name('refund-payment');
