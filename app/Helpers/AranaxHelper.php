<?php
//GET APPLICATION NAME

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

use Illuminate\Support\Facades\Http;

if (!function_exists('app_name')) {
    function app_name() {
        return config('app.name');
    }
}

//GET APPLICATION URL
if (!function_exists('app_url')) {
    function app_url() {
        return config('app.url');
    }
}

//flash message
if (!function_exists('flash_message')) {
    function flash_message()
    {
        if (Session::has('message')) {
            list($type, $message) = explode('|', Session::get('message'));
            if ($type == 'error') {
                $type = 'danger';
            } elseif ($type == 'warning') {
                $type = 'warning';
            }elseif ($type == 'success') {
                $type = 'success';
            }

            return '<div class="flash-message alert border-' . $type . ' flash-message">' . $message . '</div>';
        }

        return '';
    }
}

//CHANGE DATE FORMATE OF A DATE
if (!function_exists('formatDate')) {
    function formatDate($date, $fromFormat = 'Y-m-d', $toFormat = 'd-M-Y')
    {
        $dt = new DateTime();
        if ($date != null) {
            $datetime = $dt->createFromFormat($fromFormat, $date)->format($toFormat);
            return $datetime;
        } else {
            return '---';
        }
    }
}

if (!function_exists('formatAsIndianCurrency')) {
    function formatAsIndianCurrency($number) {
        $formatted_number = number_format($number, 2, '.', ',');
        $formatted_number = '&#8377; ' . $formatted_number;
        return $formatted_number;
    }
}

//get formated amount
if (!function_exists('getAmountInWords')) {
    function getAmountInWords($number) {
        $decimal = round($number - ($no = floor($number)), 2) * 100;
        $hundred = null;
        $digits_length = strlen($no);
        $i = 0;
        $str = array();
        $words = array(0 => '', 1 => 'One', 2 => 'Two',
            3 => 'Three', 4 => 'Four', 5 => 'Five', 6 => 'Six',
            7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
            10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve',
            13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen',
            16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen',
            19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty',
            40 => 'Forty', 50 => 'Fifty', 60 => 'Sixty',
            70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety');
        $digits = array('', 'Hundred','Thousand','Lakh', 'Crore');
        while( $i < $digits_length ) {
            $divider = ($i == 2) ? 10 : 100;
            $number = floor($no % $divider);
            $no = floor($no / $divider);
            $i += $divider == 10 ? 1 : 2;
            if ($number) {
                $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
                $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
                $str [] = ($number < 21) ? $words[$number].' '. $digits[$counter]. $plural.' '.$hundred:$words[floor($number / 10) * 10].' '.$words[$number % 10]. ' '.$digits[$counter].$plural.' '.$hundred;
            } else $str[] = null;
        }
        $Rupees = implode('', array_reverse($str));
        $paise = ($decimal > 0) ? " " . ($words[$decimal / 10] . " " . $words[$decimal % 10]) . ' Paise' : '';
        return ($Rupees ? $Rupees . 'Rupees ' : '') . $paise;
    }
}

//REMOVE ARRAY KEYS
if (!function_exists('removeKeys')) {
    function removeKeys(&$array) {
        foreach ($array as &$value) {
            if (is_array($value)) {
                // Recursive call for arrays
                removeKeys($value);
            } else {
                // Check if the current element is an array before unsetting keys
                if (is_array($array)) {
                    unset($array['id'], $array['status']);
                }
            }
        }
    }
}

//generate random code
if (!function_exists('generateRandomCode')) {
    function generateRandomCode($length = 6)
    {
        $possible_letters = '23456789BCDFGHJKMNPQRSTVWXYZ';
        $code = '';
        for ($x = 0; $x < $length; $x++) {
            $code .= ($num = substr($possible_letters, mt_rand(0, strlen($possible_letters) - 1), 1));
        }
        return $code;
    }
}

//GENERATE UNIQUE ALPHANUMERIC RANDOM NUMBER
if(!function_exists('generateUniqueAlphanumericNumber')) {
    function generateUniqueAlphanumericNumber($length = 17) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $uniqueId = '';

        while (strlen($uniqueId) < $length) {
            $uniqueId .= $characters[rand(0, $charactersLength - 1)];
        }

        $time_milli = (int) round(microtime(true) * 1000);

        return $uniqueId . $time_milli;
    }
}

if (!function_exists('maskInfo')) {
    function maskInfo($data, $type)
    {
        $mask_data = '';
        if (strlen($data) > 0) {
            if ($type == 'phone') {
                $mask_data = substr($data, 0, 2) . 'XXXXXX' . substr($data, -2);
            } else if ($type == 'email') {
                if (strpos($data, '@') !== false) {
                    list($first, $last) = explode('@', $data);
                    if (strlen($first) > 3) {
                        $max = strlen($first) > 7 ? 7 : strlen($first);
                        $first = str_replace(substr($first, '3'), str_repeat('x', $max), $first);
                    } else {
                        $n = strlen($first) - 1;
                        $first = str_replace(substr($first, $n), str_repeat('x', strlen($first) - $n), $first);
                    }

                    $last = explode('.', $last);

                    $last_domain = str_replace(substr($last['0'], '1'), str_repeat('x', strlen($last['0']) - 1), $last['0']);

                    $mask_data = $first . '@' . $last_domain . '.' . $last['1'];
                } else {
                    $mask_data = $data;
                }
            } else if ($type == 'aadhaar') {
                $mask_data = 'XXXXXXXX' . substr($data, -4);
            } else if ($type == 'pan') {
                $mask_data = 'XXXXXX' . substr($data, -4);
            } else {
                $mask_data = $data;
            }
        }
        return $mask_data;
    }
}


if (!function_exists('encryptInfo')) {
    function encryptInfo($data, $ciphering = "AES-128-CBC") {
        $iv_length = openssl_cipher_iv_length($ciphering);
        $options = 0;
        $encryption_iv = env('ENC_IV');
        $encryption_key = openssl_digest(env('HASH_SALT'), 'MD5', TRUE);

        $encryption = openssl_encrypt(
            $data,
            $ciphering,
            $encryption_key,
            $options,
            $encryption_iv
        );

        return $encryption;
    }
}

if (!function_exists('decryptInfo')) {
    function decryptInfo($data, $ciphering = "AES-128-CBC") {
        $iv_length = openssl_cipher_iv_length($ciphering);
        $options = 0;

        $decryption_iv = env('ENC_IV');
        $decryption_key = openssl_digest(env('HASH_SALT'), 'MD5', TRUE);

        $decryption = openssl_decrypt(
            $data,
            $ciphering,
            $decryption_key,
            $options,
            $decryption_iv
        );

        return $decryption;
    }
}

if (!function_exists('encryptHEXFormat')) {
    function encryptHEXFormat($data, $key){
        return bin2hex(openssl_encrypt($data, 'aes-256-ecb', $key, OPENSSL_RAW_DATA));
    }
}

if (!function_exists('decryptHEXFormat')) {
    function decryptHEXFormat($data, $key){
        return trim(openssl_decrypt(hex2bin($data), 'aes-256-ecb', $key, OPENSSL_RAW_DATA));
    }
}

//GET GRIPS CHECKSUM
if (!function_exists('hash256Checksum')) {
    function hash256Checksum($input) {
        $hash = hash("sha256", utf8_encode($input));

        return $hash;
    }
}

//GET GRIPS ENCRYPTION
if (!function_exists('gripsEncrypt')) {
    function gripsEncrypt($paydata) {
        $algo   =   "aes-128-cbc";
        $key    =   "1234567890123456";
        $iv     =   "abcdefghijklmnop";

        $cipherText = openssl_encrypt(
            $paydata,
            $algo,
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );
        $cipherText = base64_encode($cipherText);
        return $cipherText;
    }
}

//GET GRIPS DECRYPTION
if (!function_exists('gripsDecrypt')) {
    function gripsDecrypt($paydata) {
        $algo   =   "aes-128-cbc";
        $key    =   "1234567890123456";
        $iv     =   "abcdefghijklmnop";

        $cipherText = base64_decode($paydata);
        $plaintext = openssl_decrypt(
            $cipherText,
            $algo,
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );

        return $plaintext;
    }
}

if (!function_exists('print_estamp')) {
    function print_estamp($stamp_num){
        $url    =   env('ESTAMP_URL') . "api/estamp/" . $stamp_num;
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            /* CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => FALSE,
            CURLOPT_SSL_VERIFYHOST => FALSE,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(),
            CURLOPT_HTTPHEADER => array(), */
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }
}

if (!function_exists('estamp_grn_details')) {
    function estamp_grn_details($grn_num){
        $url    =   "https://uat.wbifms.gov.in/GRIPS/ChallanDetails/query.do?GRN_NO=" . $grn_num;

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $data   =   simplexml_load_string($response);

        return $data;
    }
}

if (!function_exists('udin_api_url')) {
    function udin_api_url(){
		$url="https://udin.nltr.org";
        return $url;
    }
}

if (!function_exists('udin_generate_auth_token')) {
    function udin_generate_auth_token($username="estamp",$password="QKBBQD5",$encKey="MMQNRV"){
        //snltr,HVHRVS3,112233
        $password   =   encryptHEXFormat($password, $encKey);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => udin_api_url().'/api/auth/genearte-auth-token',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => FALSE,
            CURLOPT_SSL_VERIFYHOST => FALSE,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'username='.$username.'&password='.$password.'&encKey='.$encKey,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded',
                'Cookie: document_management_system_session=uIFBmT7X50s0TyHLweYkaiRs2jqMQZ01Odqk6DA7'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        $auth   =   json_decode($response, true);

        if($auth['error']) {
            return $auth;
        }
        else {
            $x_api_token    =   $auth['x_api_token'];
            $random         =   $auth['random_key'];

            Session::put('udin_x_api_token', $x_api_token);
            Session::put('udin_random_key', $random);

            return $auth;
        }
    }
}

if (!function_exists('udin_user_phone_login')) {
    function udin_user_phone_login($phone){
        $auth   =   udin_generate_auth_token();

        if(!$auth['error']) {

            $x_api_token    =   Session::get('udin_x_api_token');
            $random         =   Session::get('udin_random_key');
            $phone          =   encryptHEXFormat($phone, $random);

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => udin_api_url().'/api/individual/login/request-otp',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_SSL_VERIFYPEER => FALSE,
                CURLOPT_SSL_VERIFYHOST => FALSE,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => array(
                    'phone' => $phone,
                    'random' => $random),
                CURLOPT_HTTPHEADER => array(
                    'X-Api-Token: '.$x_api_token,
                    'Cookie: document_management_system_session=uIFBmT7X50s0TyHLweYkaiRs2jqMQZ01Odqk6DA7'
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            return $response;
        }
        else {
            return json_encode(array('error' => true, 'message' => 'Server authentication failed'));
        }
    }
}

if (!function_exists('udin_user_phone_login_otp')) {
    function udin_user_phone_login_otp($phone, $otp){
        $x_api_token    =   Session::get('udin_x_api_token');
        $random         =   Session::get('udin_random_key');
        $enc_phone      =   encryptHEXFormat($phone, $random);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => udin_api_url().'/api/individual/login/verify-otp',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => FALSE,
            CURLOPT_SSL_VERIFYHOST => FALSE,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'phone'     =>  $enc_phone,
                "otp"       =>  $otp,
                'random'    =>  $random),
            CURLOPT_HTTPHEADER => array(
                'X-Api-Token: '.$x_api_token
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }
}

if (!function_exists('udin_user_aadhaar_register')) {
    function udin_user_aadhaar_register($aadhaar){
        $auth           =   udin_generate_auth_token();

        if(!$auth['error']) {
            $x_api_token    =   Session::get('udin_x_api_token');
            $random         =   Session::get('udin_random_key');
            $aadhaar        =   encryptHEXFormat($aadhaar, $random);

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => udin_api_url().'/api/individual/register/request-aadhaar',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_SSL_VERIFYPEER => FALSE,
                CURLOPT_SSL_VERIFYHOST => FALSE,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => array('aadhar_no' => $aadhaar,'random' => $random),
                CURLOPT_HTTPHEADER => array(
                    'X-Api-Token: ' . $x_api_token
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            return $response;
        }
        else {
            return json_encode(array('error' => true, 'message' => 'Server authentication failed'));
        }

    }
}

if (!function_exists('udin_user_aadhaar_validate_otp')) {
    function udin_user_aadhaar_validate_otp($otp){
        $x_api_token    =   Session::get('udin_x_api_token');
        $random         =   Session::get('udin_random_key');
        $trans_id       =   Session::get('udin_trans_id');

        $curl           =   curl_init();

        $url = udin_api_url().'/api/individual/register/validate-aadhar-otp';

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => FALSE,
            CURLOPT_SSL_VERIFYHOST => FALSE,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('trans_id' => $trans_id, 'otp' => $otp, 'random' => $random),
            CURLOPT_HTTPHEADER => array(
                'X-Api-Token: ' . $x_api_token
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }
}

if (!function_exists('udin_user_create_profile')) {
    function udin_user_create_profile($phone){
        $x_api_token    =   Session::get('udin_x_api_token');
        $random         =   Session::get('udin_random_key');
        $trans_id       =   Session::get('udin_trans_id');
        $phone          =   encryptHEXFormat($phone, $random);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => udin_api_url().'/api/individual/register/create-profile',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => FALSE,
            CURLOPT_SSL_VERIFYHOST => FALSE,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('trans_id' => $trans_id,'phone' => $phone,'random' => $random),
            CURLOPT_HTTPHEADER => array(
                'X-Api-Token: '.$x_api_token
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }
}

if (!function_exists('udin_user_aadhaar_details')) {
    function udin_user_aadhaar_details(){
        $x_api_token    =   Session::get('udin_x_api_token');
        $random         =   Session::get('udin_random_key');
        $token          =   Session::get('udin_token');

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => udin_api_url().'/api/profile/profile-detail',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => FALSE,
            CURLOPT_SSL_VERIFYHOST => FALSE,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('token' => $token, 'random' => $random),
            CURLOPT_HTTPHEADER => array(
                'X-Api-Token: '.$x_api_token
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }
}

if (!function_exists('checkAndVerifyRequest')) {
    function checkAndVerifyRequest($user_id, $agreement_id, $secret_code){
        $now    =   date('Y-m-d H:i:s');
        $status =   1;

        DB::beginTransaction();
        //check agreement accept
        $req_data = DB::table('cosign_requests')->where('cr_agreement_id', $agreement_id)
                    ->where('cr_code', $secret_code)->where('has_accepted', 0)->first();

        $cr_cosigner_type   =   $req_data->cr_cosigner_type;

        $affectedReq    =   DB::table('cosign_requests')->where('cr_agreement_id', $agreement_id)
                                ->where('cr_code', $secret_code)->where('has_accepted', 0)
                                ->update([
                                    'cr_user_id'        =>  $user_id,
                                    'has_accepted'      =>  1,
                                    'acceptance_date'   =>  $now
                                ]);
        if($affectedReq > 0) {
            $affectedAgreement = 0;
            if($cr_cosigner_type == 'CO-APPLICANT') {
                $status =   2;
                $affectedAgreement    =   DB::table('agreement_master')
                                            ->where('ref_num', $agreement_id)
                                            ->update([
                                                'co_applicant_user_id'  =>  $user_id,
                                                'status'      =>  $status
                                            ]);
            }
            elseif($cr_cosigner_type == 'WITNESS-1') {
                $status =   4;
                $affectedAgreement    =   DB::table('agreement_master')
                                            ->where('ref_num', $agreement_id)
                                            ->update([
                                                'witness_1_user_id'  =>  $user_id,
                                                'status'      =>  $status
                                            ]);
            }
            elseif($cr_cosigner_type == 'WITNESS-2') {
                $status =   4;
                $affectedAgreement    =   DB::table('agreement_master')
                                            ->where('ref_num', $agreement_id)
                                            ->update([
                                                'witness_2_user_id'  =>  $user_id,
                                                'status'      =>  $status
                                            ]);
            }

            if($status == 4) {
                $agr_data = DB::table('agreement_master')->where('ref_num', $agreement_id)
                                ->whereNotNull('co_applicant_user_id')
                                ->whereNotNull('witness_1_user_id')
                                ->whereNotNull('witness_2_user_id')
                                ->where('status', 4)
                                ->first();

                if($agr_data) {
                    $status   =   $agr_data->status;

                    DB::table('agreement_master')
                            ->where('ref_num', $agreement_id)
                            ->update([
                                'status'    =>  5
                            ]);
                }

            }

            DB::commit();

            return true;
        }
        else {
            DB::rollBack();
            return false;
        }

    }
}

if (!function_exists('udin_upload_aadhaar_otp')) {
    function udin_upload_aadhaar_otp($aadhaar_num){
        $x_api_token    =   Session::get('udin_x_api_token');
        $random         =   Session::get('udin_random_key');
        $token          =   Session::get('udin_token');
        $aadhaar        =   encryptHEXFormat($aadhaar_num, $random);

        //dump($x_api_token, $random, $token, $aadhaar, $aadhaar_num);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => udin_api_url().'/api/upload/request-aadhaar-otp',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_SSL_VERIFYPEER => FALSE,
            CURLOPT_SSL_VERIFYHOST => FALSE,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('aadhar_no' => $aadhaar, 'token' => $token, 'random' => $random),
            CURLOPT_HTTPHEADER => array(
                'X-Api-Token: ' . $x_api_token
            ),
        ));

        $response = curl_exec($curl);

        //dump($response);

        curl_close($curl);
        return $response;
    }
}

if (!function_exists('udin_upload_validate_aadhaar_otp')) {
    function udin_upload_validate_aadhaar_otp($otp_num){
        $x_api_token    =   Session::get('udin_x_api_token');
        $random         =   Session::get('udin_random_key');
        $token          =   Session::get('udin_token');
        $trans_id       =   Session::get('udin_trans_id');

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => udin_api_url().'/api/upload/validate-aadhar-otp',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_SSL_VERIFYPEER => FALSE,
            CURLOPT_SSL_VERIFYHOST => FALSE,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('otp_num' => $otp_num, 'trans_id' => $trans_id, 'token' => $token, 'random' => $random),
            CURLOPT_HTTPHEADER => array(
                'X-Api-Token: ' . $x_api_token
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }
}

if (!function_exists('udin_document_rate')) {
    function udin_document_rate($doc_size, $doc_ownership, $doc_validity){
        $x_api_token    =   Session::get('udin_x_api_token');
        $random         =   Session::get('udin_random_key');
        $token          =   Session::get('udin_token');

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => udin_api_url().'/api/payment/get-udin-document-rate',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_SSL_VERIFYPEER => FALSE,
            CURLOPT_SSL_VERIFYHOST => FALSE,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('doc_size' => $doc_size, 'doc_ownership' => $doc_ownership, 'doc_validity' => $doc_validity, 'token' => $token, 'random' => $random),
            CURLOPT_HTTPHEADER => array(
                'X-Api-Token: ' . $x_api_token
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }
}

if (!function_exists('udin_document_payment')) {
    function udin_document_payment($quotation_id, $redirect_url){
        $x_api_token    =   Session::get('udin_x_api_token');
        $random         =   Session::get('udin_random_key');
        $token          =   Session::get('udin_token');

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => udin_api_url().'/api/payment/generate-udin-document-payment',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_SSL_VERIFYPEER => FALSE,
            CURLOPT_SSL_VERIFYHOST => FALSE,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('quotation_id' => $quotation_id, 'redirect_url' => $redirect_url, 'token' => $token, 'random' => $random),
            CURLOPT_HTTPHEADER => array(
                'X-Api-Token: ' . $x_api_token
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }
}

if(!function_exists('build_data_files')){
    function build_data_files($boundary, $fields, $files, $file_originalName){
        $data = '';
        $eol = "\r\n";

        $delimiter = '-------------' . $boundary;

        foreach ($fields as $name => $content) {
            $data .= "--" . $delimiter . $eol
                  . 'Content-Disposition: form-data; name="' . $name . "\"".$eol.$eol
                  . $content . $eol;
        }

        foreach ($files as $name => $content) {
            $data .= "--" . $delimiter . $eol
                  . 'Content-Disposition: form-data; name="' . 'document' . '"; filename="' . $file_originalName . '"' . $eol
                  . 'Content-Transfer-Encoding: binary'.$eol;

            $data .= $eol;
            $data .= $content . $eol;
        }
        $data .= "--" . $delimiter . "--".$eol;

        return $data;
    }
}

if (!function_exists('upload_udin_doc')) {
    function upload_udin_doc($agreement_num, $quotation_amount, $quotation_id, $transaction_ref, $filepath) {
        $x_api_token    =   Session::get('udin_x_api_token');
        $random         =   Session::get('udin_random_key');
        $token          =   Session::get('udin_token');

        $doc_visibility =   'PUBLIC';
        $doc_type       =   'A';
        $key_word       =   '';

        $fields =   array(
            'token'             =>  $token,
            'random'            =>  $random,
            'quotation_id'      =>  $quotation_id,
            'transaction_id'    =>  $transaction_ref,
            'doc_visibility'    =>  $doc_visibility,
            'doc_type'          =>  $doc_type,
            'key_word'          =>  $key_word
        );

        $file_originalName  =   $agreement_num . ".pdf";
        $filenames  =   array($filepath);
        $files      =   array();
        foreach ($filenames as $f){
            $files[$f] = file_get_contents($f);
        }
        //print_r($files);exit();
        $boundary = uniqid();
        $delimiter = '-------------' . $boundary;

        $post_data = build_data_files($boundary, $fields, $files, $file_originalName);
        //return $post_data;
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => udin_api_url().'/api/upload/udin-doc',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => FALSE,
            CURLOPT_SSL_VERIFYHOST => FALSE,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $post_data,
            CURLOPT_HTTPHEADER => array(
                'X-Api-Token:'.$x_api_token,
                "Content-Type: multipart/form-data; boundary=" . $delimiter,
                "Content-Length: " . strlen($post_data)
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return $response;
    }
}

if (!function_exists('get_udin_by_quotation')) {
    function get_udin_by_quotation($quotation_id) {
        $x_api_token    =   Session::get('udin_x_api_token');
        $random         =   Session::get('udin_random_key');
        $token          =   Session::get('udin_token');

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => udin_api_url().'/api/upload/get-udin-number',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_SSL_VERIFYPEER => FALSE,
            CURLOPT_SSL_VERIFYHOST => FALSE,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('quotation_id' => $quotation_id, 'token' => $token, 'random' => $random),
            CURLOPT_HTTPHEADER => array(
                'X-Api-Token: ' . $x_api_token
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }
}

if (!function_exists('udin_add_signer')) {
    function udin_add_signer($udin_number, $signer_name, $signer_phone, $signer_statement, $signer_role) {
        $x_api_token    =   Session::get('udin_x_api_token');
        $random         =   Session::get('udin_random_key');
        $token          =   Session::get('udin_token');

        $signer_phone   =   encryptHEXFormat($signer_phone, $random);
        $root_url       =   substr(app_url(), 0, -1);
        $link           =   "sign-document";

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => udin_api_url().'/api/documnet/add-signer',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_SSL_VERIFYPEER => FALSE,
            CURLOPT_SSL_VERIFYHOST => FALSE,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('udin_id' => $udin_number, 'signer_name' => $signer_name, 'signer_phone' => $signer_phone, 'signer_statement' => $signer_statement, 'signer_role' => $signer_role, 'root_url' => $root_url, 'link' => $link, 'token' => $token, 'random' => $random),
            CURLOPT_HTTPHEADER => array(
                'X-Api-Token: ' . $x_api_token
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }
}

if (!function_exists('generate_udin_signer_phone')) {
    function generate_udin_signer_phone($mobile_num, $udin_num) {
        $auth   =   udin_generate_auth_token();

        if(!$auth['error']) {
            $x_api_token    =   Session::get('udin_x_api_token');
            $random         =   Session::get('udin_random_key');
            $token          =   Session::get('udin_token');

            $mobile_num   =   encryptHEXFormat($mobile_num, $random);

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => udin_api_url().'/api/documnet/validate-signer-phone',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_SSL_VERIFYPEER => FALSE,
                CURLOPT_SSL_VERIFYHOST => FALSE,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => array('phone' => $mobile_num, 'udin' => $udin_num, 'random' => $random),
                CURLOPT_HTTPHEADER => array(
                    'X-Api-Token: ' . $x_api_token
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            return $response;
        }
        else {
            return json_encode(array('error' => true, 'message' => 'Server authentication failed'));
        }
    }
}

if (!function_exists('validate_udin_signer_phone_otp')) {
    function validate_udin_signer_phone_otp($mobile_num, $otp_num) {
        $x_api_token    =   Session::get('udin_x_api_token');
        $random         =   Session::get('udin_random_key');
        $token          =   Session::get('udin_token');

        $mobile_num   =   encryptHEXFormat($mobile_num, $random);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => udin_api_url().'/api/documnet/validate-signer-phone-otp',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_SSL_VERIFYPEER => FALSE,
            CURLOPT_SSL_VERIFYHOST => FALSE,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('phone' => $mobile_num, 'otp' => $otp_num, 'random' => $random),
            CURLOPT_HTTPHEADER => array(
                'X-Api-Token: ' . $x_api_token
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }
}

if (!function_exists('udin_signer_aadhaar_request_otp')) {
    function udin_signer_aadhaar_request_otp($aadhaar, $udin_num, $phone_num) {
        $x_api_token    =   Session::get('udin_x_api_token');
        $random         =   Session::get('udin_random_key');
        $token          =   Session::get('udin_token');

        $aadhaar        =   encryptHEXFormat($aadhaar, $random);
        $phone_num      =   encryptHEXFormat($phone_num, $random);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => udin_api_url().'/api/documnet/validate-signer-aadhaar',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_SSL_VERIFYPEER => FALSE,
            CURLOPT_SSL_VERIFYHOST => FALSE,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('phone' => $phone_num, 'aadhaar' => $aadhaar, 'udin' => $udin_num, 'random' => $random),
            CURLOPT_HTTPHEADER => array(
                'X-Api-Token: ' . $x_api_token
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }
}

if (!function_exists('udin_signer_aadhaar_validate_otp')) {
    function udin_signer_aadhaar_validate_otp($udin_num, $phone_num, $otp_num, $trans_id) {
        $x_api_token    =   Session::get('udin_x_api_token');
        $random         =   Session::get('udin_random_key');
        $token          =   Session::get('udin_token');

        $phone_num      =   encryptHEXFormat($phone_num, $random);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => udin_api_url().'/api/documnet/validate-signer-aadhaar-otp',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_SSL_VERIFYPEER => FALSE,
            CURLOPT_SSL_VERIFYHOST => FALSE,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('phone' => $phone_num, 'udin' => $udin_num, 'otp' => $otp_num, 'trans_id' => $trans_id, 'random' => $random),
            CURLOPT_HTTPHEADER => array(
                'X-Api-Token: ' . $x_api_token
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }
}

if (!function_exists('udin_user_document_sign_complete')) {
    function udin_user_document_sign_complete($udin_num, $phone_num, $account_id, $signer_statement) {
        $x_api_token    =   Session::get('udin_x_api_token');
        $random         =   Session::get('udin_random_key');
        $token          =   Session::get('udin_token');

        $phone_num      =   encryptHEXFormat($phone_num, $random);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => udin_api_url().'/api/documnet/final-sign',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_SSL_VERIFYPEER => FALSE,
            CURLOPT_SSL_VERIFYHOST => FALSE,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('phone' => $phone_num, 'udin' => $udin_num, 'accountID' => $account_id, 'statement' => $signer_statement, 'random' => $random),
            CURLOPT_HTTPHEADER => array(
                'X-Api-Token: ' . $x_api_token
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }
}

if (!function_exists('udin_final_generation')) {
    function udin_final_generation($udin_num, $signer_content_order) {
        $x_api_token    =   Session::get('udin_x_api_token');
        $random         =   Session::get('udin_random_key');
        $token          =   Session::get('udin_token');

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => udin_api_url().'/api/documnet/final-udin-generation',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_SSL_VERIFYPEER => FALSE,
            CURLOPT_SSL_VERIFYHOST => FALSE,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('udin' => $udin_num, 'signer_content_order' => $signer_content_order, 'token' => $token, 'random' => $random),
            CURLOPT_HTTPHEADER => array(
                'X-Api-Token: ' . $x_api_token
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }
}

if (!function_exists('get_udin_details')) {
    function get_udin_details($udin_num){
        $x_api_token    =   Session::get('udin_x_api_token');
        $random         =   Session::get('udin_random_key');
        $token          =   Session::get('udin_token');

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => udin_api_url().'/api/verify/udin',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_SSL_VERIFYPEER => FALSE,
            CURLOPT_SSL_VERIFYHOST => FALSE,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('udin' => $udin_num, 'token' => $token, 'random' => $random),
            CURLOPT_HTTPHEADER => array(
                'X-Api-Token: ' . $x_api_token
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }
}

if (!function_exists('get_udin_doc_validity')) {
    function get_udin_doc_validity($validity_months){
        $validity = str_replace(" MONTH", "", str_replace(" MONTHS", "", $validity_months));

        if ($validity >= 1 && $validity <= 12) {
            return 1;
        } elseif ($validity > 12) {
            return ceil($validity / 12);
        } else {
            return 0; // Handle cases where input is less than 1 month
        }
    }
}


if (!function_exists('initiateSmsActivation')) {
    function initiateSmsActivation($phone_number, $message, $template = 'AUTH_OTP') {
        /* if (env('APP_ENV') == "local") {
            return true;
        } */
        $templateid =   null;

        if ($template == 'AUTH_OTP') {
            $templateid =   '1407167283338942618';
        } else if ($template == 'CO_SIGN_REQ') {
            $templateid =   '1007246532139143742';  //'1407170446070246918';
        } else if ($template == 'WITNESS_REQ') {
            $templateid =   '1007595665796880978';
        }

        $params = array(
            'mobile'    =>  urlencode($phone_number),
            'message'   =>  urlencode($message),
            'templateid' =>  $templateid,
            'passkey'   =>  'sms_ChukTipatra_123',
            'extra'     =>  ''
        );

        // print_r($params);exit();
        $params_string = "";
        foreach ($params as $key => $value) {
            $params_string .= $key . '=' . $value . '&';
        }
        rtrim($params_string, '&');
        // dd($params_string);
        $smsurl     =    'https://barta.wb.gov.in/send_sms_chuktipatra.php';
        $headers= array(
            'Content-Type: application/x-www-form-urlencoded'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $smsurl);
        curl_setopt($ch, CURLOPT_HEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, count($params));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params_string);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($status == 200) {
            curl_close($ch);
            return true;
        } else {
            return false;
        }
        return true;
       /* $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://barta.wb.gov.in/send_sms_chuktipatra.php',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => 'mobile=6294199181&message=You%20have%20been%20requested%20to%20co-sign%20Agreement%20Number%201709790867354.%20Your%20secret%20code%20is%20C9XXYJ.%20DITE%20GoWB&templateid=1007246532139143742&extra=&passkey=sms_ChukTipatra_123',
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/x-www-form-urlencoded'
          ),

          CURLOPT_SSL_VERIFYPEER => false, // Disable SSL verification
          CURLOPT_SSL_VERIFYHOST => false, // Disable SSL host verification
        ));

        $response = curl_exec($curl);

        curl_close($curl); */
    }
}
 // function to generate a six digit otp
if (!function_exists('generateOTP')) {
    function generateOTP()
    {
        $possible_letters = '1234567890';
        $code = '';
        for ($x = 0; $x < 6; $x++) {
            $code .= ($num = substr($possible_letters, mt_rand(0, strlen($possible_letters) - 1), 1));
        }
        return $code;
    }
}
