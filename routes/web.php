<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\eagreement\PageController;
use App\Http\Controllers\eagreement\UserController;
use App\Http\Controllers\eagreement\AgreementController;
use App\Http\Controllers\eagreement\PaymentController;

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

Route::get('/', [PageController::class, 'showHomepage']);
Route::get('/user-authenticate', [UserController::class, 'showLogin']);
Route::post('/user-authenticate', [UserController::class, 'generateLoginOtp']);
Route::post('/validate-login-otp', [UserController::class, 'validateLoginOtp']);

Route::get('/user-registration', [UserController::class, 'showRegistration']);
Route::post('/generate-aadhaar-otp', [UserController::class, 'generateAadhaarOtp']);
Route::post('/validate-aadhaar-otp', [UserController::class, 'validateAadhaarOtp']);
Route::post('/register-profile', [UserController::class, 'registerProfile']);

Route::get('/verify-request', [AgreementController::class, 'showVerifyRequest']);
Route::post('/verify-request', [AgreementController::class, 'saveVerifyRequest']);

Route::get('/sign-document', [AgreementController::class, 'showSignDocument']);
Route::post('/generate-sign-document-otp', [AgreementController::class, 'generateSignDocumentOtp']);
Route::post('/validate-sign-document-otp', [AgreementController::class, 'validateSignDocumentOtp']);
Route::post('/generate-sign-document-aadhaar-otp', [AgreementController::class, 'generateSignDocumentAadhaarOtp']);
Route::post('/validate-sign-document-aadhaar-otp', [AgreementController::class, 'validateSignDocumentAadhaarOtp']);
Route::post('/sign-document-complete', [AgreementController::class, 'completeSignDocument']);


//Route::group(['middleware' => ['verify.user']], function () {

    Route::get('/dashboard', [UserController::class, 'showDashboard']);
    Route::get('/logout', [UserController::class, 'showLogout']);

    Route::get('/basic-details', [AgreementController::class, 'showBasicDetail']);
    Route::post('/save-basic-detail', [AgreementController::class, 'saveBasicDetail']);

    Route::get('/property-details', [AgreementController::class, 'showPropertyDetail']);
    Route::post('/save-property-detail', [AgreementController::class, 'savePropertyDetail']);

    Route::get('/contract-details', [AgreementController::class, 'showContractDetail']);
    Route::post('/save-contract-detail', [AgreementController::class, 'saveContractDetail']);

    Route::get('/estamp-purchase', [AgreementController::class, 'eStampPurchase']);
    Route::post('/estamp-purchase', [AgreementController::class, 'eStampPurchaseSave']);
    Route::post('/estamp-response', [AgreementController::class, 'saveEStampResponse']);

    Route::post('/co-app-request', [AgreementController::class, 'saveCoAppRequest']);
    Route::post('/witness-request', [AgreementController::class, 'saveWitnessRequest']);

    Route::get('/upload-document/{ref_num}', [AgreementController::class, 'uploadDocument']);
    Route::post('/generate-upload-aadhaar-otp', [AgreementController::class, 'generateUploadAadhaarOtp']);
    Route::post('/validate-upload-aadhaar-otp', [AgreementController::class, 'validateUploadAadhaarOtp']);
    Route::post('/udin-payment-submit', [PaymentController::class, 'doPayment']);
    Route::post('/payment-response', [PaymentController::class, 'getPaymentResponse']);
    Route::get('/get-payment/{ref_num}', [PaymentController::class, 'getPayment']);
    Route::post('/upload-udin', [AgreementController::class, 'uploadGetUdin']);

    Route::get('/co-app-sign-request/{ref_num}', [AgreementController::class, 'coAppSignRequest']);
    Route::get('/witness-sign-request/{num}/{ref_num}', [AgreementController::class, 'witnessSignRequest']);

    
    Route::post('/generate-final-document', [AgreementController::class, 'generateFinalDocument']);
    Route::post('/get-final-udin', [AgreementController::class, 'getFinalUdin']);
    

    Route::post('/download-udin-document', [AgreementController::class, 'downloadUdinDocument']);


    Route::get('/draft-agreement/{ref_num}', [AgreementController::class, 'getDraftAgreement']);

//});

Route::get('/agreement/{ref_num}', [AgreementController::class, 'getAgreementData']);

Route::get('/check-sms', [PageController::class, 'showSms']);
Route::post('/validate-sms', [PageController::class, 'validateSms']);
