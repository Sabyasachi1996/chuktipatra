<?php

namespace App\Http\Controllers\eagreement\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\eagreement\admin\AdminUser;
use App\Models\eagreement\User;
use App\Models\eagreement\Token;
use App\Models\eagreement\Agreement;
use App\Models\eagreement\CoSignRequest;
use App\Models\eagreement\Payment;
use App\Models\eagreement\PaymentDetail;

use Exception;
use Validator;
use DB;
use Session;
use DateTime;
use PDF;

class AdminController extends Controller {

    protected $auth;
    public $back_url = null;

    public function __construct() {
        //$this->auth = new Authentication();
    }
//admin login page
   public function adminLoginPage(){
      return view('pages.admin.adminLoginPage',[]);
   }
//admin dashboard page
public function adminDashboardPage(){
    // dd("passing");
    $query= "SELECT COUNT(id) AS quantity,CASE 
    WHEN status = 0 THEN 'ESTAMP PURCHASED'
    WHEN status = 1 THEN 'CO-SIGNER REQUEST SENT'
    WHEN status = 2 THEN 'CO-SIGNER ACCEPTED'
    WHEN status = 3 THEN 'WITNESS REQUEST SENT'
    WHEN status = 4 THEN 'WITNESS SIGNED PARTIAL'
    WHEN status = 5 THEN 'UPLOAD PENDING'
    WHEN status = 6 THEN 'PROVISIONAL UDIN GENERATED'
    WHEN status = 7 THEN 'CO-APPLICANT REQUEST SENT'
    WHEN status = 8 THEN 'CO-APPLICANT SIGNED'
    WHEN status = 9 THEN 'WITNESS ONE REQUEST SENT'
    WHEN status = 10 THEN 'WITNESS ONE SIGNED'
    WHEN status = 11 THEN 'WITNESS TWO REQUEST SENT'
    WHEN status = 12 THEN 'CO-SIGNING COMPLETED'
    WHEN status = 90 THEN 'UDIN GENERATION IN-PRGRESS'
    WHEN status = 99 THEN 'UDIN GENERATED'
    ELSE 'E-AGREEMENT GENERATED BUT ESTAMP PAYMENT FAILED'
    END AS status FROM `agreement_master` GROUP BY status;";
    $data=DB::select($query);
    // dd($data);
    return view('pages.admin.adminDashboardPage')->with('data',$data);
}
//agreement list page
public function agreementListPage(){
    $agreements = DB::table('agreement_master')
                    ->join('users','agreement_master.applicant_user_id','=','users.user_id')
                    ->get();
// dd($agreements);
    return view('pages.admin.agreementListPage', [
        'agreements'    =>  $agreements
    ]);
}
//admin login send otp
public function adminLoginSendOtp(Request $request){
    $now= date("Y-m-d H:i:s");
    $encPhone= $request->adminPhone;
    $random= $request->random;
    $phoneDecrypted= decryptHexFormat($encPhone,$random);
    $phoneEnc= encryptInfo($phoneDecrypted);
    $adminData= AdminUser::where('admin_phone',$phoneEnc)->first();
    if(!empty($adminData)){
        $otp= generateOTP(6);
        $otpExpiry= date('Y-m-d H:i:s',strtotime('+10 minutes',strtotime($now)));
        $sms_message = "OTP to login is " . $otp . " for UDIN Portal. DITE GoWB";
               
                 initiateSmsActivation($phoneDecrypted, $sms_message, 'AUTH_OTP');
        $adminUpdateData= array(
                               "admin_last_otp"=>$otp,
                               "admin_otp_created_on"=>$now,
                               "admin_otp_expired_on"=>$otpExpiry,
                            );
        $AdminUpdateOperation= AdminUser::where('admin_phone',$phoneEnc)->update($adminUpdateData);
        

                return response()->json([
                    'error'         =>  false,
                    'message'       =>  'An OTP has been successfully sent to ' . $phoneDecrypted,
                    'otp'           =>  env('APP_DEBUG') ? $otp : ''
                ]);
    }else{
        $response= array(
            "error"=> true,
            "message"=> "No Administration Member Found Associated With This Number",
            "random"=> generateRandomCode(32)
        );
        return response(json_encode(response),200);
    }
}
//admin login verify otp
public function adminLoginVerifyOtp(Request $request){
    // dd($request->all());
    $now= date('Y-m-d H:i:s');
    $encPhone= $request->adminPhone;
    $otp= $request->adminOTP;
    $random= $request->random;
    $phoneDecrypted= decryptHexformat($encPhone,$random);
    $phoneEnc= encryptInfo($phoneDecrypted);
    // dd($now." ".$otp." ".$phoneEnc);
    $adminData= AdminUser::where('admin_phone',$phoneEnc)
                          ->where('admin_last_otp',$otp)
                          ->where('admin_otp_expired_on','>=',$now)
                          ->first();
    if(!empty($adminData)){
        // dd($adminData);
        $adminName= $adminData->admin_name;
        $adminId= $adminData->admin_id;
        Session::put('adminLogin',true);
        Session::put('adminName',$adminName);
        Session::put('adminPhone',$phoneDecrypted);
        Session::put('adminId',$adminId);
        AdminUser::where('admin_id',$adminId)
                 ->update(array(
                    "admin_last_login_on"=>$now
                    ));   
        $response= array(
            "error"=>false,
            "message"=> "You Are Logged In Successfully,Redirecting To The Dashboard",
            "adminLogin"=>true,
            "adminName"=>$adminName,
            "adminPhone"=>$phoneDecrypted,
            "adminId"=>$adminId,
        );
    }else{
        // dd("empty");
        $response= array(
            "error"=>true,
            "message"=> "Wrong Credentials, Kindly Check The Data You Are Providing",
        );
    }                      
   return response(json_encode($response),200);
}
//admin logout
public function adminLogout(Request $request){
    Session::flush();
    $reponse = array(
        'error'                 => false,
        'message'               =>  "You Have Been Successfully Logged Out!",
        'aadhaar_verified'      =>  false,
        'udin_token'            =>  null,
        'udin_token_expired_on' =>  null,
        'udin_profile_type'     =>  null,
        'udin_profile_id'       =>  null,
        'udin_profile'          =>  null

    );
    return response(json_encode($reponse), 200)

    ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
    ->header('Cache-Control', 'post-check=0, pre-check=0', false)
    ->header("Pragma", "no-cache")
    ->header('Clear-Site-Data', '"cache", "cookies", "storage", "executionContexts", "*"');
}
}    