<?php

use App\Http\Controllers\Admin\PlanesController;
use App\Http\Controllers\Conversation\ConversationController;
use App\Http\Controllers\Conversation\IaController;
use App\Http\Controllers\Conversation\AnnexController;
use App\Http\Controllers\Conversation\StructureController;
use App\Http\Controllers\Coupon\CouponController;
use App\Http\Controllers\Document\DocumentController;
use App\Http\Controllers\Package\PackageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Payment\PagoController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\TokenUsageController;
use App\Http\Controllers\Payment\WebhookController;

// Ruta de prueba: usuario logueado
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// Grupo de rutas Auth
Route::prefix('auth')->group(function ($router) {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/forgot-password-code',[AuthController::class,'forgotPasswordCode']);
    Route::post('/verify-code',[AuthController::class,'verifyCode']);
    Route::post('/reset-password-code',[AuthController::class,'resetPasswordCode']);
    Route::post('/refresh', [AuthController::class, 'refresh'])->middleware('auth:api')->name('refresh');
});

// Webhook externo (sin auth)
Route::post('/mp/webhook', [WebhookController::class, 'handle']);
Route::get('packages',[PackageController::class,'index']);
// Grupo protegido con middleware auth
Route::middleware('auth:api')->group(function ($router) {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/profile', [AuthController::class, 'updateProfile']);

    // =========================================================================
    // RUTAS EXCLUSIVAS PARA ADMINISTRADOR (role_id = 1)
    // =========================================================================

    Route::middleware('role:1')->group(function () {

        // Gestión de Usuarios
        Route::get('/users', [UsersController::class, 'index']);
        Route::get('/users/{id}', [UsersController::class, 'show']);
        Route::post('/users', [UsersController::class, 'store']);
        Route::patch('/users/{id}', [UsersController::class, 'update']);

        // Gestión de Planes
        Route::post('/plans', [PlanesController::class, 'store']);
        Route::patch('/plans/{id}', [PlanesController::class, 'update']);
        Route::patch('/plans/{id}/status', [PlanesController::class, 'toggleStatus']);

        // Gestión de Paquetes
        Route::post('packages', [PackageController::class, 'store']);
        Route::patch('packages/{id}', [PackageController::class, 'update']);
        Route::post('packages/{id}/plans', [PackageController::class, 'syncPackagePlans']);

        // Gestión de Cupones
        Route::post('coupon/savecoupon',[CouponController::class,'saveCoupon']);
        Route::get('coupons',[CouponController::class,'index']);

        // Gestión de Pagos
        Route::get('/payment/getpayments', [PagoController::class, 'getPayments']);
        Route::post('/payment/{id}/approve', [PagoController::class, 'approvePayment']);
        Route::post('/payment/{id}/reject', [PagoController::class, 'rejectPayment']);

        
        Route::get('/admin/stats', [DashboardController::class, 'getAdminStats']);
        Route::get('/admin/plan-performance', [DashboardController::class, 'getPlanPerformance']);
        Route::get('/admin/monthly-earnings', [DashboardController::class, 'getMonthlyEarnings']);

        // Módulo "Tokens por usuario"
        Route::get('/admin/token-usage', [TokenUsageController::class, 'index']);
 });

    // =========================================================================
    // RUTAS COMUNES / USUARIO Y ADMIN
    // =========================================================================

    // Catálogo de Planes y Paquetes (Lectura)
    Route::get('/plans', [PlanesController::class, "index"]);
    Route::get('/plans/getplanid', [PlanesController::class, "getPlanId"]);
    Route::get('/plans/{id}', [PlanesController::class, 'show']);
    Route::get('section/obtenercapitulosplan',[PlanesController::class,'obtenerCapitulosPlan']);
    
        
    Route::get('packages/{id}', [PackageController::class, 'show']);
    Route::get('getpackageplans',[PackageController::class,'getPackagePlans']);
    
    // Conversaciones
    Route::post('/conversation/validateAnswerResponse',[IaController::class,'validateAnswerResponse']);
    Route::post('/conversation/startconversation',[ConversationController::class,'startConversation']);
    Route::post('/conversation/updatetitleconversation',[ConversationController::class,'updateTitleConversation']);
    Route::post('/conversation/savereply',[ConversationController::class,'conversationSaveReply']);
    Route::post('/conversation/saveanswerdiagnostic',[ConversationController::class,'saveAnswerDiagnostic']);
    Route::post('/conversation/assistant-chat', [ConversationController::class, 'assistantChat']);

    Route::get('/conversation/conversationsUser',[ConversationController::class,'conversationsUser']);
    Route::get('/conversation/conversationplanuser',[ConversationController::class,'getConversationPlanUser']);
    Route::get('/conversation/closestructure',[ConversationController::class,'closeStructure']);
    Route::get('/conversation/viewconversationplanuser',[ConversationController::class,'getViewConversationPlanUser']);
    Route::get('/conversation/replicate/prediction/{id}',  [IaController::class, 'getReplicatePrediction']);
    Route::get('conversation/verficationdiagnosticexist',[ConversationController::class,'getVerficationDiagnosticExist']);
    Route::get('conversation/diagnosticplan',[ConversationController::class,'getDiagnosticoPlan']);
    Route::get('conversation/conversationPlan',[ConversationController::class,'getConversationPlan']);
    Route::post('/conversation/delete-chapters-answers', [ConversationController::class, 'deleteChaptersAnswers']);
    Route::post('/conversation/go-back', [ConversationController::class, 'goBack']);

    // Anexos del plan (imágenes o Excel al final del documento)
    Route::get('/conversation/annexes', [AnnexController::class, 'index']);
    Route::post('/conversation/annexes', [AnnexController::class, 'store']);
    Route::patch('/conversation/annexes/{id}/move', [AnnexController::class, 'move']);
    Route::delete('/conversation/annexes/{id}', [AnnexController::class, 'destroy']);
    
    // Pagos del usuario
    Route::post('payment/validate', [CouponController::class, 'validateCoupon']);
    Route::post('/payment', [PagoController::class, 'payment']);
    Route::post('/payment/yape', [PagoController::class, 'registerYapePayment']);
    Route::post('/payment/free', [PagoController::class, 'registerFree']);

    // Documentos y diagnóstico
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
    });

Route::get('/test-zip', [DocumentController::class, 'testZip']);