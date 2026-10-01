<?php

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('login',[ApiController::class,'login']);
Route::post('forgetPassword',[ApiController::class,'forgetPassword']);
Route::post('verifyotp', [ApiController::class, 'verifyOtp']);
Route::post('resend_otp', [ApiController::class, 'resend_otp']);
Route::post('resetPassword', [ApiController::class, 'resetPassword']);
Route::get('relation-managers', [ApiController::class, 'getRelationManager']);


//register channel partner
Route::post('channel/register',[ApiController::class,'channelPartner_register']);

Route::group(['middleware'=>['auth:api']],function(){
    Route::get('get_user',[ApiController::class,'get_users']);
    Route::get('orgDashboardApi',[ApiController::class,'orgDashboardApi']);

    //partner
    Route::get('partner/index',[ApiController::class,'partner_index']);

    //project
    Route::get('project/index',[ApiController::class,'project_index']);

    // developer 
    Route::get('developer/index',[ApiController::class,'developer_index']);
    Route::get('developer/project/index',[ApiController::class,'developer_project_index']);



    //units
    Route::get('project/units/{id}',[ApiController::class,'project_units_index']);

    //project material
    Route::get('projectdetail/{id}',[ApiController::class,'project_detail_index']);

    //property
    Route::get('property/index',[ApiController::class,'property_index']);

    //
    Route::get('leads/index',[ApiController::class,'leads_index']);
    Route::get('leads/views/{id}',[ApiController::class,'leads_views']);
    Route::post('leads/store',[ApiController::class,'leads_store']);

    //change password
    Route::post('change_password',[ApiController::class,'change_password']);
    //Profile Update
    Route::get('user/profile/',[ApiController::class,'getUserProfile']);
    Route::post('user/update/profile/{id}',[ApiController::class,'updatePartnerandUserProfile']);


    Route::post('leads/tasks/store', [ApiController::class, 'storeLeadTasks']);
    Route::get('leads/tasks/activity/{lead}',[ApiController::class,'getLeadTaskActivity']);
    Route::post('leads/offer/store/{lead}', [ApiController::class, 'storeLeadOffers']);
    Route::get('leads/offer/activity/{lead}',[ApiController::class,'getLeadOffers']);
    Route::get('units',[ApiController::class,'getUnitRef']);

    Route::post('contact_inquiry',[ApiController::class,'contactStore']);
    
    Route::get('user/destroy',[ApiController::class,'userDestroy']);


});
