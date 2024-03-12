<?php

namespace App\Http\Controllers\eagreement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\eagreement\User;
use App\Models\eagreement\Token;

use Exception;
use Validator;
use DB;
use Session;
use DateTime;

class UserController extends Controller {

    protected $auth;
    public $back_url = null;

    public function __construct() {
        //$this->auth = new Authentication();
    }

    public function showLogin(Request $request) {
        if($request->session()->exists('user_id')){
            return redirect('/dashboard');
        }
        return view('pages.auth.login', []);
    }

    //validate login
    public function generateLoginOtp(Request $request) {
        $mobile_num =   $request->mobile_num;
        $random     =   $request->random;

        $decrypted_phone  =   decryptHEXFormat($mobile_num, $random);

        try{
            $phone_res  =   json_decode(udin_user_phone_login($decrypted_phone), true);
            Session::put('udin_random_key', $phone_res['random_key']);

            if(!$phone_res['error']){
                Session::put('user_phone', $decrypted_phone);

                //otp sent and return
                $reponse = array(
                    'message'   =>  $phone_res['message']
                );
                return response(json_encode($reponse), 200);
            }    
            else {
                if($phone_res['code'] == 'IND_00012') {
                    $reponse = array(
                        'error'     =>  true,
                        'message'   =>  $phone_res['message']
                    );
                    return response(json_encode($reponse), 200);
                }
                else {
                    $reponse = array(
                        'error'     =>  true,
                        'message'   =>  "Your profile is not found, please register"
                    );
                    return response(json_encode($reponse), 200);
                }
                
            }
        }
        catch(Exception $e) {
            $reponse = array(
                'error'     =>  true,
                'message'   =>  'Some error has occurred. Please try again.'
            );
            return response(json_encode($reponse), 200);
        }    
    }

    //validate login OTP
    public function validateLoginOtp(Request $request) {
        $now        =   date("Y-m-d H:i:s");
        $otp_num    =   $request->otp_num;
        $phone_num  =   Session::get("user_phone");
        $phone_enc  =   encryptInfo($phone_num);

        try{
			$otp_res    =   json_decode(udin_user_phone_login_otp($phone_num, $otp_num), true);
            
            if(!is_null($otp_res)){
                Session::put('udin_random_key', $otp_res['random_key']);

                if(!$otp_res['error']){
					Session::put("udin_phone", $phone_num);
					Session::put('udin_token', $otp_res['token']);

                    //get user fullname and address from UDIN
                    $profile_res    =   json_decode(udin_user_aadhaar_details(), true);
                   
                    Session::put('udin_random_key', $profile_res['random_key']);

                    $aadhaar_number     =   decryptHEXFormat($profile_res['profile'][0]['self']['aadhaar_no'], Session::get('udin_random_key'));
                    
                    $aadhaar_fullname   =   strtoupper(decryptHEXFormat($profile_res['profile'][0]['self']['fullname'], Session::get('udin_random_key')));
                    
                    $aadhaar_address    =   strtoupper(decryptHEXFormat($profile_res['profile'][0]['self']['address'], Session::get('udin_random_key')));

                    Session::put("user_aadhaar", $aadhaar_number);
                    Session::put("user_fullname", $aadhaar_fullname);
                    Session::put("user_address", $aadhaar_address);

                    if(DB::table("users")->where("user_phone", $phone_enc)->where("user_is_active", "1")->exists()){
                        $user   =   DB::table("users")->where("user_phone", $phone_enc)->first();
                        
                        Session::put('user_id', $user->user_id);
                    }
                    else{
                        //save to eAgreement DB
                        $user_data = array(
                            "user_phone"            =>  $phone_enc,
                            "user_aadhaar_num"      =>  $aadhaar_number,
                            "user_aadhaar_name"     =>  $aadhaar_fullname,
                            "user_aadhaar_address"  =>  $aadhaar_address,
                            "user_aadhaar_dob"      =>  null,
                            "user_aadhaar_photo"    =>  "",
                            "user_is_udin_verified" =>  "1",
                            "user_is_active"        =>  "1",
                            "user_created_on"       =>  $now,
                            "user_updated_on"       =>  $now
                        );

                        User::insert($user_data);
                        $user_id = DB::getPdo()->lastInsertId();

                        Session::put('user_id', $user_id);
                    }

                    if(Session::get('req_verify_agreement_id') != '') {
                        checkAndVerifyRequest(Session::get('user_id'), Session::get('req_verify_agreement_id'), Session::get('req_verify_secret_code'));
                    }

                    $reponse = array(
                        'message'           =>  "Profile authenticated",
                        'aadhaar_fullname'  =>  $aadhaar_fullname,
                        'aadhaar_address'   =>  $aadhaar_address
                    );
                    return response(json_encode($reponse), 200);
                }
                else {
                    $reponse = array(
                        'error'     =>  true,
                        'message'   =>  $otp_res['message']
                    );
                    return response(json_encode($reponse), 200); 
                }    
            } 
            else{
                $reponse = array(
                    'error'     =>  true,
                    'message'   =>  "Unable to process your request"
                );
                return response(json_encode($reponse), 200);
            }   

        }
        catch(Exception $e) {
            //echo $e->getMessage();
            $reponse = array(
                'error'     =>  true,
                'message'   =>  $e->getMessage()
            );
            return response(json_encode($reponse), 200); 
        }    
    }    

    public function showRegistration(Request $request) {
        if($request->session()->exists('user_id')){
            return redirect('/dashboard');
        }
        return view('pages.auth.register', []);
    }

    //generate aadhaar otp
    public function generateAadhaarOtp(Request $request) {
        $aadhaar_num    =   $request->aadhaar_num;
        $random         =   $request->random;

        $decrypted_aadhaar  =   decryptHEXFormat($aadhaar_num, $random);

        try {
            $aadhaar_res    =   json_decode(udin_user_aadhaar_register($decrypted_aadhaar), true);

            if(!$aadhaar_res['error']){
                Session::put('udin_random_key', $aadhaar_res['random_key']);
                if(!$aadhaar_res['error']){
                    Session::put('udin_trans_id', $aadhaar_res['trans_id']);
                    Session::put('user_aadhaar', $decrypted_aadhaar);

                    //otp sent and return
                    $reponse = array(
                        'message'   =>  $aadhaar_res['message']
                    );
                    return response(json_encode($reponse), 200);
                }
            }
            else {
                $reponse = array(
                    'error'     =>  true,
                    'message'   =>  $aadhaar_res['message']
                );
                return response(json_encode($reponse), 200);
            }

        }
        catch(Exception $e) {
            $reponse = array(
                'error'     =>  true,
                'message'   =>  $e->getMessage() //'Some error has occurred. Please try again.'
            );
            return response(json_encode($reponse), 200);
        }
    }
    
    //validate aadhaar otp
    public function validateAadhaarOtp(Request $request) {
        $otp_num    =   $request->otp_num;
        $otp_res    =   json_decode(udin_user_aadhaar_validate_otp($otp_num),true);

        if(!is_null($otp_res)){
            Session::put('udin_random_key', $otp_res['random_key']);
            
            if(!$otp_res['error']){
                Session::put('user_fullname', $otp_res['fullname']);
                Session::put('user_address', $otp_res['address']);

                $reponse = array(
                    'message'   =>  $otp_res['message'],
                    'aadhaar_fullname'  =>  Session::get('user_fullname'),
                    'aadhaar_address'   =>  Session::get('user_address')
                );
                return response(json_encode($reponse), 200);
            }
            else{
                $reponse = array(
                    'error'     =>  true,
                    'message'   =>  $otp_res['message']
                );
                return response(json_encode($reponse), 200); 
            }
        }
        else{
            $reponse = array(
                'error'     =>  true,
                'message'   =>  "Unable to process your request"
            );
            return response(json_encode($reponse), 200);
        }
    }

    //register profile
    public function registerProfile(Request $request) {
        //save to UDIN DB
        $profile_mobile =   $request->profile_mobile;
        $phone_res      =   json_decode(udin_user_create_profile($profile_mobile), true);


        if(!is_null($phone_res)){
            Session::put('udin_random_key', $phone_res['random_key']);

            $aadhaar_number =   maskInfo(Session::get("user_aadhaar"), 'aadhaar');

            if(!$phone_res['error']){
                $phone_enc  =   encryptInfo($profile_mobile);
                $now        =   date("Y-m-d H:i:s");
                
                //save to eAgreement DB
                $user_data = array(
                    "user_phone"            =>  $phone_enc,
                    "user_aadhaar_num"      =>  $aadhaar_number,
                    "user_aadhaar_name"     =>  Session::get('user_fullname'),
                    "user_aadhaar_address"  =>  Session::get('user_address'),
                    "user_aadhaar_dob"      =>  null,
                    "user_aadhaar_photo"    =>  "",
                    "user_is_udin_verified" =>  "1",
                    "user_is_active"        =>  "1",
                    "user_created_on"       =>  $now,
                    "user_updated_on"       =>  $now
                );

                User::insert($user_data);
                $user_id = DB::getPdo()->lastInsertId();

                if(Session::get('req_verify_agreement_id') != '') {
                    checkAndVerifyRequest($user_id, Session::get('req_verify_agreement_id'), Session::get('req_verify_secret_code'));
                }

                $request->session()->invalidate();

                return redirect('/user-authenticate')->with("message", "success|" . $phone_res['message']); 
            }
            else{
                return redirect()->back()->with("message", "error|" . $phone_res['message']);  
            }
        }
        else{
            return redirect()->back()->with("message", "error|Unable to process your request. Please try again.");
        }

    }

    //dashboard
    public function showDashboard(Request $request) {
        $user_id = Session::get('user_id');

        $agreements = DB::table('agreement_master')
                        ->where('applicant_user_id', $user_id)
                        ->orWhere('co_applicant_user_id', $user_id)
                        ->orWhere('witness_1_user_id', $user_id)
                        ->orWhere('witness_2_user_id', $user_id)
                        ->orderBy('created_at', 'DESC')->get();

        return view('pages.auth.dashboard', [
            'agreements'    =>  $agreements
        ]);
    }

    //logout
    public function showLogout(Request $request) {
        Session::flush();
        return redirect('/');
    }


}    