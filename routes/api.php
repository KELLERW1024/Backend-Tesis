<?php

use App\Http\Controllers\Admin\PlanesController;
use App\Http\Controllers\Conversation\ConversationController;
use App\Http\Controllers\Conversation\IaController;
use App\Http\Controllers\Conversation\StructureController;
use App\Http\Controllers\Coupon\CouponController;
use App\Http\Controllers\Document\DocumentController;
use App\Http\Controllers\Package\PackageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Payment\PagoController;
use App\Http\Controllers\Payment\WebhookController;


// Ruta de prueba: usuario logueado
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// Grupo de rutas Auth
Route::prefix('auth')->group(function ($router) {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    //Route::post('/forgot-password',[AuthController::class,'forgotPassword']);
    Route::post('/forgot-password-code',[AuthController::class,'forgotPasswordCode']);
    Route::post('/reset-password',[AuthController::class,'resetPassword']);
    //Route::post('/verify-code',[AuthController::class,'verifyCode']);
    Route::post('/reset-password-code',[AuthController::class,'resetPasswordCode']);


});

// Grupo protegido con middleware auth
Route::middleware('auth:api')->group(function ($router) {

    // List-Users
    Route::get('/users', [UsersController::class, 'index']);
    Route::get('/users/{id}', [UsersController::class, 'show']);
    Route::post('/users', [UsersController::class, 'store']);
    Route::patch('/users/{id}', [UsersController::class, 'update']);

    // Users
    // Route::post("users/{id}",[UsersController::class,"update"]);
    Route::get('/plans', [PlanesController::class, "index"]);
    Route::get('/plans/getplanid', [PlanesController::class, "getPlanId"]);
    Route::get('/plans/{id}', [PlanesController::class, 'show']);
    Route::post('/plans', [PlanesController::class, 'store']);
    Route::patch('/plans/{id}', [PlanesController::class, 'update']);
    Route::patch('/plans/{id}/status', [PlanesController::class, 'toggleStatus']);
    Route::get('section/obtenercapitulosplan',[PlanesController::class,'obtenerCapitulosPlan']);
    Route::get('section/obtenercapitulosplan',[PlanesController::class,'obtenerCapitulosPlan']);

    Route::get('packages',[PackageController::class,'index']);
    Route::get('packages/{id}', [PackageController::class, 'show']);
    Route::post('packages', [PackageController::class, 'store']);
    Route::patch('packages/{id}', [PackageController::class, 'update']);
    Route::get('getpackageplans',[PackageController::class,'getPackagePlans']);
    Route::post('packages/{id}/plans', [PackageController::class, 'syncPackagePlans']);



    Route::post('/conversation/validateAnswerResponse',[IaController::class,'validateAnswerResponse']);
    // Route::post('/conversation/validateanswer',[IaController::class,'validateAnswer']);

    Route::post('/conversation/startconversation',[ConversationController::class,'startConversation']);
    Route::post('/conversation/updatetitleconversation',[ConversationController::class,'updateTitleConversation']);

    Route::post('/conversation/savereply',[ConversationController::class,'conversationSaveReply']);
    Route::post('/conversation/saveanswerdiagnostic',[ConversationController::class,'saveAnswerDiagnostic']);

    //Route::get('/conversation/conversationprogress',[ConversationController::class,'conversationProgress']);
    Route::get('/conversation/conversationsUser',[ConversationController::class,'conversationsUser']);
    Route::get('/conversation/conversationplanuser',[ConversationController::class,'getConversationPlanUser']);

    Route::get('/conversation/closestructure',[ConversationController::class,'closeStructure']);

    Route::get('/conversation/viewconversationplanuser',[ConversationController::class,'getViewConversationPlanUser']);

    Route::get( '/conversation/replicate/prediction/{id}',  [IaController::class, 'getReplicatePrediction']);


    Route::get('conversation/verficationdiagnosticexist',[ConversationController::class,'getVerficationDiagnosticExist']);

    Route::get('conversation/diagnosticplan',[ConversationController::class,'getDiagnosticoPlan']);
    Route::get('conversation/conversationPlan',[ConversationController::class,'getConversationPlan']);

    Route::post('coupon/savecoupon',[CouponController::class,'saveCoupon']);
    Route::get('coupons',[CouponController::class,'index']);
    Route::post('payment/validate', [CouponController::class, 'validateCoupon']);

    Route::post('/payment', [PagoController::class, 'payment']);
    Route::post('/payment/yape', [PagoController::class, 'registerYapePayment']);
    Route::post('/payment/free', [PagoController::class, 'registerFree']);

    Route::get('/payment/getpayments', [PagoController::class, 'getPayments']);

    Route::post('/payment/{id}/approve', [PagoController::class, 'approvePayment']);
    Route::post('/payment/{id}/reject', [PagoController::class, 'rejectPayment']);

    Route::post('/mp/webhook', [WebhookController::class, 'handle']);



    Route::get('/download',[DocumentController::class,'generarDocument']);


    Route::get('/diagnostic/nextquestion',[DocumentController::class,'nextQuestion']);

    // ============================================
    // STRUCTURE
    // ============================================
    
    Route::prefix('structure')->group(function () {

        Route::get('/plan/{planId}', [StructureController::class, 'index']);

        Route::get('/{id}', [StructureController::class, 'show']);

        Route::post('/', [StructureController::class, 'store']);

        Route::patch('/{id}', [StructureController::class, 'update']);

        Route::delete('/{id}', [StructureController::class, 'destroy']);

        Route::patch('/{id}/position', [StructureController::class, 'position']);

    });


    Route::get('/plan/{planId}', [StructureController::class, 'index']);

    Route::post('/', [StructureController::class, 'store']);

    Route::get('/{id}', [StructureController::class, 'show']);

    Route::patch('/{id}', [StructureController::class, 'update']);

    Route::delete('/{id}', [StructureController::class, 'destroy']);

    Route::patch('/{id}/position', [StructureController::class, 'position']);

    
});

Route::get('/test-zip', [DocumentController::class, 'testZip']);