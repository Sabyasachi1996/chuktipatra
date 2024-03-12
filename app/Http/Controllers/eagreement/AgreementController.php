<?php

namespace App\Http\Controllers\eagreement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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

class AgreementController extends Controller {

    protected $auth;
    public $back_url = null;

    public function __construct() {
        //$this->auth = new Authentication();
    }

    //show basic detail
    public function showBasicDetail(Request $request) {
        return view('pages.property.basic_details', []);
    }

    //save basic detail
    public function saveBasicDetail(Request $request) {
        $lessor_lessee  =   $request->lessor_lessee;
        Session::put('lessor_lessee', $lessor_lessee);

        return redirect('property-details');
    }

    //show property detail
    public function showPropertyDetail(Request $request) {
        return view('pages.property.property_details', []);
    }

    public function savePropertyDetail(Request $request) {
        $now    =   date('Y-m-d H:i:s');
        $today  =   date('Y-m-d');

        //do validation

        try {
            $agreement_ref_num = (int) round(microtime(true) * 1000);

            //save agreement data
            $property_detail    =   array(
                'property_type'     =>  $request->property_type,
                'property_floor'    =>  $request->property_floor,
                'property_room'     =>  $request->property_room,
                'property_bed'      =>  $request->property_bed,
                'property_bath'     =>  $request->property_bath,
                'property_balcony'  =>  $request->property_balcony,
                'property_area'     =>  $request->property_area,
                'property_parking'  =>  $request->property_parking
            );

            $property_data = [
                'ref_num'               =>  $agreement_ref_num,
                'applicant_type'        =>  Session::get('lessor_lessee'),
                'applicant_user_id'     =>  Session::get('user_id'),
                'property_type'         =>  $request->property_type,
                'property_detail'       =>  json_encode($property_detail),
                'property_address'      =>  strtoupper($request->property_address),
                'property_state'        =>  strtoupper($request->property_state),
                'property_city'         =>  strtoupper($request->property_city),
                'property_pin'          =>  $request->property_pin,
                'created_at'            =>  $now
            ];

            Agreement::insert($property_data);
            $agreement_id = DB::getPdo()->lastInsertId();

            if($agreement_id > 0) {
                Session::put('agreement_ref_num', $agreement_ref_num);
                return redirect('contract-details')->with("message", "success|Property details saved");
            }
            else {
                return redirect()->back()->with("message", "error|Unable to process your request");
            }

        }
        catch(Exception $e) {
            //echo $e->getMessage(); exit();
            return redirect()->back()->with("message", "error|Unable to process your request");
        }
    }

    //showContractDetail
    public function showContractDetail(Request $request) {
        return view('pages.property.contract_details', []);
    }

    //save contract detail
    public function saveContractDetail(Request $request) {
        $now    =   date('Y-m-d H:i:s');

        //do validation

        try {
            $agreement_ref_num = Session::get('agreement_ref_num');

            //save agreement data
            $contract_detail    =   array(
                'agreement_start'       =>  $request->agreement_start,
                'agreement_duraion'     =>  $request->agreement_duraion,
                'rent_pay_day'          =>  $request->rent_pay_day,
                'rent_amount'           =>  $request->rent_amount,
                'maintenance_amount'    =>  $request->maintenance_amount,
                'security_amount'       =>  $request->security_amount,
                'notice_period'         =>  $request->notice_period,
                'estamp_amt'            =>  env('ESTAMP_AMOUNT')
            );

            $property_data = [
                'ref_num'           =>  $agreement_ref_num,
                'contract_detail'   =>  $contract_detail
            ];

            Agreement::where('ref_num', $agreement_ref_num)
                        ->update($property_data);

            return redirect('estamp-purchase')->with("message", "success|Contract details saved");;
        }
        catch(Exception $e) {
            return redirect()->back()->with("message", "error|Unable to process your request");
        }
    }

    //show eStamp purchase
    public function eStampPurchase(Request $request) {
        $agreement_ref_num = Session::get('agreement_ref_num');

        $agreement = DB::table('agreement_master')
                        ->leftJoin('users', 'user_id', '=', 'applicant_user_id')
                        ->select('user_aadhaar_name', 'user_phone', 'user_email', 'user_aadhaar_address')
                        ->where('ref_num', $agreement_ref_num)
                        ->first();

        $estamp_data    =   array(
            'fullname'  =>  $agreement->user_aadhaar_name,
            'phone'     =>  decryptInfo($agreement->user_phone),
            'email'     =>  $agreement->user_email,
            'address'   =>  $agreement->user_aadhaar_address,
            'purpose'   =>  'eSTAMP PURCHASE',
            'amount'    =>  env('ESTAMP_AMOUNT'),
            'return_url'=>  app_url() . "estamp-response"
        );
        // dd($estamp_data);
        return view('pages.payment.purchase', [
            'agreement_ref_num' =>  $agreement_ref_num,
            'estamp_url'        =>  env('ESTAMP_URL') . 'payment-submit',
            'estamp_amount'     =>  env('ESTAMP_AMOUNT'),
            'enc_estamp'        =>  base64_encode(json_encode($estamp_data))
        ]);
    }

    public function saveEStampResponse(Request $request) {
        $now        =   date('Y-m-d H:i:s');
        $ref_num    =   decryptHEXFormat($request->crn, 'eSt@mp');
        $grn_num    =   decryptHEXFormat($request->grn, 'eSt@mp');
        $esr        =   json_decode(decryptHEXFormat($request->esr, 'eSt@mp'));
        if(isset($ref_num) && (isset($grn_num)) ) {
            //get GRN details
            $grn_resp   =   estamp_grn_details($grn_num);
            //update db
            // if($grn_num == $grn_resp->GRN_NO) {
                Agreement::where("ref_num", $ref_num)
                    ->update([
                        "pg_grn"        =>  $grn_num,
                        "estamp_num"    =>  $esr->res->eStampNum,
                        "estamp_amt"    =>  $esr->res->eStampAmt,
                        "estamp_date"   =>  $esr->res->eStampDate,
                        "status"        =>  0
                    ]);

                //return to dashboard finally
                return redirect('dashboard');
            /*}
            else {
                return redirect()->back()->with("message", "error|Unable to process your request");
            }*/
        }
        else {
            return redirect()->back()->with("message", "error|Invalid data submitted");
        }
    }

    //save co-app request
    public function saveCoAppRequest(Request $request) {
        $now            =   date('Y-m-d H:i:s');
        $agreement_id   =   $request->agreement_id;
        $mobile_num     =   $request->mobile_num;
        $enc_mobile_num =   encryptInfo($request->mobile_num);

        if(
            CoSignRequest::where('cr_agreement_id', $agreement_id)
                ->where('cr_phone', $enc_mobile_num)
                ->where('has_accepted', 0)
                ->where('cr_cosigner_type', 'CO-APPLICANT')
                ->exists()) {

            return redirect()->back()->with("message", "error|Request already sent");
        }
        else {
            try {
                DB::beginTransaction();
                $secret_code = generateRandomCode();

                $data = array(
                    'cr_agreement_id'   =>  $agreement_id,
                    'cr_cosigner_type'  =>  'CO-APPLICANT',
                    'cr_phone'          =>  $enc_mobile_num,
                    'cr_code'           =>  $secret_code,
                    'created_on'        =>  $now
                );

                CoSignRequest::insert($data);
                $req_id = DB::getPdo()->lastInsertId();

                if($req_id > 0) {
                    Agreement::where("ref_num", $agreement_id)
                        ->update([
                            "status" => 1
                        ]);

                    DB::commit();

                    //send SMS
                    $sms_message = "You have been requested to co-sign Agreement Number " . $agreement_id . ". Your secret code is " . $secret_code . ". DITE GoWB";
                    initiateSmsActivation($mobile_num, $sms_message, 'CO_SIGN_REQ');

                    return redirect()->back()->with("message", "success|Request sent successfully");
                }
                else {
                    DB::rollBack();
                    return redirect()->back()->with("message", "error|Unable to process your request");
                }
            }
            catch(Exception $e) {
                DB::rollBack();
                return redirect()->back()->with("message", "error|Unable to process your request");
            }
        }

    }

    //save saveWitnessRequest
    public function saveWitnessRequest(Request $request) {
        $now            =   date('Y-m-d H:i:s');
        $agreement_id   =   $request->agreement_id2;
        $mobile_num1     =   $request->mobile_num1;
        $mobile_num2     =   $request->mobile_num2;
        $enc_mobile_num1 =   encryptInfo($request->mobile_num1);
        $enc_mobile_num2 =   encryptInfo($request->mobile_num2);

        if(
            CoSignRequest::where('cr_agreement_id', $agreement_id)
                ->where('cr_phone', $enc_mobile_num1)
                ->where('has_accepted', 0)
                ->where('cr_cosigner_type', 'WITNESS-1')
                ->exists()

                ||

            CoSignRequest::where('cr_agreement_id', $agreement_id)
                ->where('cr_phone', $enc_mobile_num2)
                ->where('has_accepted', 0)
                ->where('cr_cosigner_type', 'WITNESS-2')
                ->exists()
        ) {

            return redirect()->back()->with("message", "error|Request already sent");
        }
        else {
            try {
                DB::beginTransaction();
                $inserted = 0;

                $secret_code1 = generateRandomCode();
                $secret_code2 = generateRandomCode();

                $data1 = array(
                    'cr_agreement_id'   =>  $agreement_id,
                    'cr_cosigner_type'  =>  'WITNESS-1',
                    'cr_phone'          =>  $enc_mobile_num1,
                    'cr_code'           =>  $secret_code1,
                    'created_on'        =>  $now
                );

                $data2 = array(
                    'cr_agreement_id'   =>  $agreement_id,
                    'cr_cosigner_type'  =>  'WITNESS-2',
                    'cr_phone'          =>  $enc_mobile_num2,
                    'cr_code'           =>  $secret_code2,
                    'created_on'        =>  $now
                );

                CoSignRequest::insert([$data1, $data2]);

                Agreement::where("ref_num", $agreement_id)
                    ->update([
                        "status" => 3
                    ]);

                DB::commit();

                //send SMS
                $sms_message1 = "You have been requested to witness Agreement Number " . $agreement_id . ". Your secret code is " . $secret_code1 . ". DITE GoWB";
                initiateSmsActivation($mobile_num1, $sms_message1, 'WITNESS_REQ');

                $sms_message2 = "You have been requested to witness Agreement Number " . $agreement_id . ". Your secret code is " . $secret_code2 . ". DITE GoWB";
                initiateSmsActivation($mobile_num2, $sms_message2, 'WITNESS_REQ');

                return redirect()->back()->with("message", "success|Request sent successfully");
            }
            catch(Exception $e) {
                DB::rollBack();
                //echo $e->getMessage();
                return redirect()->back()->with("message", "error|Unable to process your request");
            }
        }
    }

    //show verify request
    public function showVerifyRequest(Request $request) {
        return view('pages.auth.verify_request', [
        ]);
    }

    //save verify request
    public function saveVerifyRequest(Request $request) {
        $agreement_id   =   $request->agreement_id;
        $secret_code    =   $request->secret_code;

        if(
            CoSignRequest::where('cr_agreement_id', $agreement_id)
                ->where('cr_code', $secret_code)
                ->where('has_accepted', 0)
                ->exists()) {

            Session::put('req_verify_agreement_id', $agreement_id);
            Session::put('req_verify_secret_code', $secret_code);

            //send to UDIN authentication
            return redirect('user-authenticate');
        }
        else {
            return redirect()->back()->with("message", "error|Unable to process your request due to invalid data");
        }
    }

    //upload document
    public function uploadDocument(Request $request, $ref_num) {
        $agreement = DB::table('agreement_master')
                        ->leftJoin('users', 'user_id', '=', 'applicant_user_id')
                        ->where('ref_num', $ref_num)
                        ->first();

        if($agreement == null) {
            return redirect()->back()->with("message", "error|Unable to find the agreement");
        }
        else {
            $is_upload_aadhaar_authenticated = Session::get('aadhar_verified_token') == null ? false : true;

            $quotation_amount   =   '';
            $quotation_id       =   '';
            $redirect_url       =   '';
            $transaction_ref    =   '';

            if($is_upload_aadhaar_authenticated) {
                $contract_detail    =   json_decode($agreement->contract_detail);
                $contract_period    =   $contract_detail->agreement_duraion;
                $doc_size           =   $agreement->file_size;
                $doc_ownership      =   'MULTI';
                $doc_validity       =   get_udin_doc_validity($contract_period);

                //call api/payment/get-udin-document-rate
                $rate_res   =   json_decode(udin_document_rate($doc_size, $doc_ownership, $doc_validity), true);
                Session::put('udin_random_key', $rate_res['random_key']);

                if(!$rate_res['error']) {

                    $quotation_amount   =   $rate_res['data']['amount'];
                    $quotation_id       =   $rate_res['data']['quotation_id'];
                    $redirect_url       =   app_url();
                    $transaction_ref    =   '';

                    //update agreement table
                    Agreement::where('ref_num', $ref_num)->update([
                        'quotation_id'      =>  $quotation_id,
                        'quotation_amount'  =>  $quotation_amount,
                        'transaction_ref'   =>  $transaction_ref,
                    ]);


                    //call GRIPS to pay
                    $grips_data    =   array(
                        'fullname'  =>  $agreement->user_aadhaar_name,
                        'phone'     =>  decryptInfo($agreement->user_phone),
                        'email'     =>  $agreement->user_email,
                        'address'   =>  $agreement->user_aadhaar_address,
                        'purpose'   =>  'UDIN FEES FOR AGREEMENT',
                        'amount'    =>  $quotation_amount,
                        'return_url'=>  app_url() . "udin-payment-response"
                    );

                    return view('pages.agreement.upload', [
                        'is_upload_aadhaar_authenticated' => $is_upload_aadhaar_authenticated,
                        'agreement_num'     =>  $ref_num,
                        'quotation_amount'  =>  $quotation_amount,
                        'quotation_id'      =>  $quotation_id,
                        'payment_url'       =>  app_url() . 'udin-payment-submit',
                        'enc_payment'       =>  base64_encode(json_encode($grips_data))
                    ]);


                }
                else {
                    return redirect()->back()->with("message", "error|" . $rate_res['error']);
                }
            }

            return view('pages.agreement.upload', [
                'is_upload_aadhaar_authenticated' => $is_upload_aadhaar_authenticated,
                'agreement_num'     =>  $ref_num,
                'quotation_amount'  =>  $quotation_amount,
                'quotation_id'      =>  $quotation_id,
                'transaction_ref'   =>  $transaction_ref
            ]);
        }

    }

    //generate upload aadhaar otp
    public function generateUploadAadhaarOtp(Request $request) {
        $aadhaar_num    =   $request->aadhaar_num;
        $agreement_num  =   $request->agreement_num;
        $random         =   $request->random;

        $decrypted_aadhaar  =   decryptHEXFormat($aadhaar_num, $random);

        try {
            $agreement = DB::table('agreement_master')
                        ->where('ref_num', $agreement_num)
                        ->first();

            if($agreement == null) {
                $reponse = array(
                    'error'     =>  true,
                    'message'   =>  "Unable to find the agreement"
                );
                return response(json_encode($reponse), 200);

                /* return redirect()->back()->with(
                    array(
                        'error'     =>  true,
                        'message'   =>  'Unable to upload the document'
                    )
                ); */
            }
            else {
                $applicant = DB::table('users')->where('user_id', $agreement->applicant_user_id)->first();
                $co_applicant = DB::table('users')->where('user_id', $agreement->co_applicant_user_id)->first();
                $w_1 = DB::table('users')->where('user_id', $agreement->witness_1_user_id)->first();
                $w_2 = DB::table('users')->where('user_id', $agreement->witness_2_user_id)->first();

                $lessor = array();
                $lessee = array();
                $witness_1 = array();
                $witness_2 = array();

                if($agreement->applicant_type == 'lessor') {
                    $lessor = array(
                                    'lessor_aadhaar'    =>  isset($applicant) ? $applicant->user_aadhaar_num : 'AADHAAR OF LESSOR',
                                    'lessor_fullname'   =>  isset($applicant) ? $applicant->user_aadhaar_name : 'FULLNAME OF LESSOR',
                                    'lessor_address'    =>  isset($applicant) ? $applicant->user_aadhaar_address : 'ADDRESS OF LESSOR'
                                );

                    $lessee = array(
                        'lessee_aadhaar'    =>  isset($co_applicant) ? $co_applicant->user_aadhaar_num : 'AADHAAR OF LESSEE',
                        'lessee_fullname'   =>  isset($co_applicant) ? $co_applicant->user_aadhaar_name : 'FULLNAME OF LESSEE',
                        'lessee_address'    =>  isset($co_applicant) ? $co_applicant->user_aadhaar_address : 'ADDRESS OF LESSEE'
                    );
                }
                else {
                    $lessor = array(
                        'lessor_aadhaar'    =>  isset($co_applicant) ? $co_applicant->user_aadhaar_num : 'AADHAAR OF LESSOR',
                        'lessor_fullname'   =>  isset($co_applicant) ? $co_applicant->user_aadhaar_name : 'FULLNAME OF LESSOR',
                        'lessor_address'    =>  isset($co_applicant) ? $co_applicant->user_aadhaar_address : 'ADDRESS OF LESSOR'
                    );

                    $lessee = array(
                        'lessee_aadhaar'    =>  isset($applicant) ? $applicant->user_aadhaar_num : 'AADHAAR OF LESSEE',
                        'lessee_fullname'   =>  isset($applicant) ? $applicant->user_aadhaar_name : 'FULLNAME OF LESSEE',
                        'lessee_address'    =>  isset($applicant) ? $applicant->user_aadhaar_address : 'ADDRESS OF LESSEE'
                    );
                }

                $witness_1 = array(
                    'witness_1_aadhaar'    =>  isset($w_1) ? $w_1->user_aadhaar_num : 'AADHAAR OF WITNESS ONE',
                    'witness_1_fullname'   =>  isset($w_1) ? $w_1->user_aadhaar_name : 'FULLNAME OF WITNESS ONE',
                    'witness_1_address'    =>  isset($w_1) ? $w_1->user_aadhaar_address : 'ADDRESS OF WITNESS ONE'
                );

                $witness_2 = array(
                    'witness_2_aadhaar'    =>  isset($w_2) ? $w_2->user_aadhaar_num : 'AADHAAR OF WITNESS TWO',
                    'witness_2_fullname'   =>  isset($w_2) ? $w_2->user_aadhaar_name : 'FULLNAME OF WITNESS TWO',
                    'witness_2_address'    =>  isset($w_2) ? $w_2->user_aadhaar_address : 'ADDRESS OF WITNESS TWO'
                );

                $pdf_data   =   array();

                $context = stream_context_create([
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                    ],
                ]);

                $estamp_img_url =   env('ESTAMP_URL') . "api/estamp/" . $agreement->estamp_num;

                $estamp_data    =   array(
                    'estamp_num'    =>  $agreement->estamp_num,
                    'estamp_url'    =>  env('ESTAMP_URL'),
                    'estamp_img'    =>  base64_encode(file_get_contents($estamp_img_url, false, $context))
                );

                $property_data      =   $agreement->property_detail;
                $property_address   =   $agreement->property_address . ", " . $agreement->property_city . ", " . $agreement->property_state . ", " . $agreement->property_pin;
                $contract_detail    =   $agreement->contract_detail;

                $pdf_data   =   array(
                    'estamp_data'       =>  json_encode($estamp_data),
                    'property_data'     =>  $property_data,
                    'property_address'  =>  json_encode($property_address),
                    'contract_detail'   =>  $contract_detail,
                    'lessor'   =>  json_encode($lessor),
                    'lessee'   =>  json_encode($lessee),
                    'witness_1'   =>  json_encode($witness_1),
                    'witness_2'   =>  json_encode($witness_2),
                );

                //print_r($pdf_data);

                $pdf = PDF::set_option('isRemoteEnabled', true)->loadView('pdf.rent_agreement', $pdf_data);

                $path = 'upload/';
                $fileName   =   $agreement_num . ".pdf";
                $filepath   =   $path  . $fileName;

                $pdf->save($filepath);

                if (file_exists($filepath)) {
                    $filesize = filesize($filepath);

                    //update agreement db
                    Agreement::where('ref_num', $agreement_num)->update(['file_path' => $filepath, 'file_size' => $filesize]);

                    //upload to UDIN - get Aadhaar OTP
                    $otp_res    =   json_decode(udin_upload_aadhaar_otp($decrypted_aadhaar), true);

                    Session::put('udin_random_key', $otp_res['random_key']);

                    if(!$otp_res['error']){
                        Session::put('udin_trans_id', $otp_res['trans_id']);

                        //otp sent and return
                        $reponse = array(
                            'message'   =>  $otp_res['message']
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
            }
        }
        catch(Exception $e) {
            $reponse = array(
                'error'     =>  true,
                'message'   =>  "Unable to process your request"
            );
            return response(json_encode($reponse), 200);
        }
    }

    //validate upload aadhaar otp
    public function validateUploadAadhaarOtp(Request $request) {
        $otp_num    =   $request->otp_num;
        $otp_res    =   json_decode(udin_upload_validate_aadhaar_otp($otp_num),true);

        if(!is_null($otp_res)){
            Session::put('udin_random_key', $otp_res['random_key']);

            if(!$otp_res['error']){
                Session::put('aadhar_verified_token', $otp_res['aadhar_verified_token']);

                $reponse = array(
                    'message'   =>  $otp_res['message']
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

    //upload to get udin
    public function uploadGetUdin(Request $request) {

        $client_req_id      =   $request->clientReqId;
        $agreement_num      =   $client_req_id;

        $agreement          =   Agreement::where('ref_num', $agreement_num)->first();
        $quotation_id       =   $agreement->quotation_id;
        $quotation_amount   =   $agreement->quotation_amount;
        $redirect_url       =   app_url();
        $transaction_ref    =   '';

        //call udin generate payment
        $payment_res    =   json_decode(udin_document_payment($quotation_id, $redirect_url), true);

        Session::put('udin_random_key', $payment_res['random_key']);

        if(!$payment_res['error']) {
            $transaction_ref    =   $payment_res['data']['payment_resp']['transaction_ref'];

            //update agreement table
            Agreement::where('ref_num', $agreement_num)->update([
                'transaction_ref'   =>  $transaction_ref,
            ]);
        }
        else {
            return redirect()->back()->with("message", "error|" . $payment_res['error']);
        }

        if( ($agreement_num != '') && ($quotation_amount != '') && ($quotation_id != '') && ($transaction_ref != '') ) {
            $agreement = Agreement::where('ref_num', $agreement_num)->first();

            $filepath   =   $agreement->file_path;
            //api/upload/udin-doc
            $doc_resp   =   json_decode(upload_udin_doc($agreement_num, $quotation_amount, $quotation_id, $transaction_ref, $filepath),  true);

            Session::put('udin_random_key', $doc_resp['random_key']);
            //dump($doc_resp);

            if(!$doc_resp['error']) {
                $udin_resp  =   json_decode(get_udin_by_quotation($quotation_id), true);
                Session::put('udin_random_key', $udin_resp['random_key']);

                if(!$udin_resp['error']) {
                    $udin_number    =   $udin_resp['data'][0]['udin'];

                    Agreement::where('ref_num', $agreement_num)->update([
                        'udin_num'  =>  $udin_number,
                        'status'    =>  6
                    ]);

                    return redirect("/dashboard");
                }
            }
            else {
                return redirect()->back()->with("message", "error|" . $doc_resp['message']);
            }
        }
        else {
            return redirect()->back()->with("message", "error|Required data is missing");
        }
    }

    //send sign request to co-applicant
    public function coAppSignRequest(Request $request, $ref_num) {
        $user_id    =   Session::get('user_id');

        if($ref_num != null) {
            $agreement  =   Agreement::with('coApplicant')->where('ref_num', $ref_num)
                                ->where('status', 6)->where('applicant_user_id', $user_id)->first();

            if($agreement) {
                $udin_number        =   $agreement->udin_num;
                $signer_name        =   $agreement->coApplicant->user_aadhaar_name;
                $signer_phone       =   decryptInfo($agreement->coApplicant->user_phone);
                $signer_statement   =   "Please sign the agreement as co-applicant";
                $signer_role        =   $agreement->applicant_type == 'lessor' ? 'Lessee' : 'Lessor';

                Session::put('signer_role', $signer_role);

                $signer_resp    =   json_decode(udin_add_signer($udin_number, $signer_name, $signer_phone, $signer_statement, $signer_role), true);

                if(!is_null($signer_resp)){
                    Session::put('udin_random_key', $signer_resp['random_key']);

                    if(!$signer_resp['error']){

                        Agreement::where('ref_num', $ref_num)->update(['status' => 7]);

                        return redirect()->back()->with("message", "success|" . $signer_resp['message']);
                    }
                    else{
                        return redirect()->back()->with("message", "error|" . $signer_resp['message']);
                    }
                }
                else{
                    return redirect()->back()->with("message", "error|Unable to process your request");
                }
            }
            else {
                return redirect()->back()->with("message", "error|Agreement not found");
            }
        }

    }

    //send sign request to witness
    public function witnessSignRequest(Request $request, $witness_num, $ref_num) {
        $user_id    =   Session::get('user_id');
        if($ref_num != null) {
            $agreement          =   null;
            $udin_number        =   null;
            $signer_name        =   null;
            $signer_phone       =   null;
            $signer_statement   =   null;

            if($witness_num == 1) {
                $agreement  =   Agreement::with('witnessOne')->where('ref_num', $ref_num)
                                ->where('status', 8)->where('applicant_user_id', $user_id)->first();

                if($agreement) {
                    $udin_number        =   $agreement->udin_num;
                    $signer_name        =   $agreement->witnessOne->user_aadhaar_name;
                    $signer_phone       =   decryptInfo($agreement->witnessOne->user_phone);
                    $signer_statement   =   "Please sign the agreement as witness one";
                }
            }

            if($witness_num == 2) {
                $agreement  =   Agreement::with('witnessTwo')->where('ref_num', $ref_num)
                                ->where('status', 10)->where('applicant_user_id', $user_id)->first();

                if($agreement) {
                    $udin_number        =   $agreement->udin_num;
                    $signer_name        =   $agreement->witnessTwo->user_aadhaar_name;
                    $signer_phone       =   decryptInfo($agreement->witnessTwo->user_phone);
                    $signer_statement   =   "Please sign the agreement as witness two";
                }
            }

            if($agreement) {
                $signer_role        =   ($witness_num == 1) ? 'Witness One' : 'Witness Two';
                Session::put('signer_role', $signer_role);

                $signer_resp    =   json_decode(udin_add_signer($udin_number, $signer_name, $signer_phone, $signer_statement, $signer_role), true);

                if(!is_null($signer_resp)){
                    Session::put('udin_random_key', $signer_resp['random_key']);

                    if(!$signer_resp['error']){

                        if($witness_num == 1) {
                            Agreement::where('ref_num', $ref_num)->update(['status' => 9]);
                        }

                        if($witness_num == 2) {
                            Agreement::where('ref_num', $ref_num)->update(['status' => 11]);
                        }


                        return redirect()->back()->with("message", "success|" . $signer_resp['message']);
                    }
                    else{
                        return redirect()->back()->with("message", "error|" . $signer_resp['message']);
                    }
                }
                else{
                    return redirect()->back()->with("message", "error|Unable to process your request");
                }
            }
            else {
                return redirect()->back()->with("message", "error|Agreement not found");
            }
        }

    }

    //show sign document
    public function showSignDocument(Request $request) {
        return view('pages.agreement.sign_document', [
            //'signer_role'   =>   Session::get('signer_role')
        ]);
    }

    //generate sign document OTP
    public function generateSignDocumentOtp(Request $request) {
        $mobile_num     =   $request->mobile_num;
        $udin_num       =   $request->udin_num;

        if($mobile_num != '' && $udin_num != '') {
            $phone_resp =   json_decode(generate_udin_signer_phone($mobile_num, $udin_num), true);

            if(!is_null($phone_resp)){
                Session::put('udin_random_key', $phone_resp['random_key']);

                if(!$phone_resp['error']){
                    //otp sent and return
                    $reponse = array(
                        'message'   =>  $phone_resp['message']
                    );
                    return response(json_encode($reponse), 200);
                }
                else{
                    $reponse = array(
                        'error'     =>  true,
                        'message'   =>  $phone_resp['message']
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
        else {
            $reponse = array(
                'error'     =>  true,
                'message'   =>  "Unable to process your request"
            );
            return response(json_encode($reponse), 200);
        }
    }

    //validate sign document OTP
    public function validateSignDocumentOtp(Request $request) {
        $udin_num       =   $request->udin_num;
        $mobile_num     =   $request->mobile_num;
        $otp_num        =   $request->otp_num;

        if($mobile_num != '' && $otp_num != '') {
            $otp_resp =   json_decode(validate_udin_signer_phone_otp($mobile_num, $otp_num), true);

            if(!is_null($otp_resp)){
                Session::put('udin_random_key', $otp_resp['random_key']);

                if(!$otp_resp['error']){

                    $pdf_file = file_put_contents("upload/tmp/" . $udin_num . ".pdf", base64_decode($otp_resp['content']));

                    $reponse = array(
                        'message'       =>  $otp_resp['message'],
                        'file_url'      =>  app_url() . "upload/tmp/" . $udin_num . ".pdf",
                        'statement'     =>  $otp_resp['statement'],
                        'owner'         =>  $otp_resp['owner'],
                    );
                    return response(json_encode($reponse), 200);
                }
                else{
                    $reponse = array(
                        'error'     =>  true,
                        'message'   =>  $otp_resp['message']
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
        else {
            $reponse = array(
                'error'     =>  true,
                'message'   =>  "Unable to process your request"
            );
            return response(json_encode($reponse), 200);
        }


    }

    //get document sign aadhaar otp
    public function generateSignDocumentAadhaarOtp(Request $request) {
        $udin_num       =   $request->udin_num;
        $aadhaar_num    =   $request->aadhaar_num;
        $phone_num      =   $request->phone_num;
        $random         =   $request->random;

        $decrypted_aadhaar  =   decryptHEXFormat($aadhaar_num, $random);

        try {
            $aadhaar_res    =   json_decode(udin_signer_aadhaar_request_otp($decrypted_aadhaar, $udin_num, $phone_num), true);

            if(!$aadhaar_res['error']){
                Session::put('udin_random_key', $aadhaar_res['random_key']);
                if(!$aadhaar_res['error']){
                    Session::put('udin_trans_id', $aadhaar_res['trans_id']);
                    Session::put('user_aadhaar', $decrypted_aadhaar);

                    //otp sent and return
                    $reponse = array(
                        'message'   =>  $aadhaar_res['message'],
                        'trans_id'  =>  $aadhaar_res['trans_id']
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
                'message'   =>  "Some error has occurred. Please try again."
            );
            return response(json_encode($reponse), 200);
        }
    }

    //validate document sign aadhaar otp
    public function validateSignDocumentAadhaarOtp(Request $request) {
        $udin_num   =   $request->udin_num;
        $phone_num  =   $request->phone_num;
        $otp_num    =   $request->otp_num;
        $trans_id   =   $request->trans_id;
        $otp_res    =   json_decode(udin_signer_aadhaar_validate_otp($udin_num, $phone_num, $otp_num, $trans_id),true);

        if(!is_null($otp_res)){
            Session::put('udin_random_key', $otp_res['random_key']);

            if(!$otp_res['error']){

                $signer_statement   =   "I have verified and signed the Agreement";
                $account_id         =   $otp_res['accounts'][0]['accountID'];

                $res    =   json_decode(udin_user_document_sign_complete($udin_num, $phone_num, $account_id, $signer_statement),true);

                if(!is_null($res)){
                    Session::put('udin_random_key', $res['random_key']);

                    if(!$res['error']){
                        //get last status
                        $status = Agreement::where('udin_num', $udin_num)->value('status');

                        //update agreement db
                        Agreement::where('udin_num', $udin_num)->update(['status' => $status+1]); //should be 8, 10, 12

                        $reponse = array(
                            'message'   =>  $res['message'],
                        );
                        return response(json_encode($reponse), 200);
                    }
                }
                else{
                    $reponse = array(
                        'error'     =>  true,
                        'message'   =>  $res['message']
                    );
                    return response(json_encode($reponse), 200);
                }
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

    //generate final UDIN document
    public function generateFinalDocument(Request $request) {
        $ref_num    =   $request->ref_num;

        if( Agreement::where('ref_num', $ref_num)->where('status', 12)->exists() ) {
            $agreement = Agreement::where('ref_num', $ref_num)->first();

            $udin_num       =   $agreement->udin_num;
            $quotation_id   =   $agreement->quotation_id;

            $signer_content_order   =   "This document owned by <0>, co-signed by <1>, witnessed by <2> and <3>";

            $res    =   json_decode(udin_final_generation($udin_num, $signer_content_order), true);

            if(!is_null($res)){
                Session::put('udin_random_key', $res['random_key']);

                if(!$res['error']){
                    $affected = Agreement::where('ref_num', $ref_num)->update([
                                    'status'    =>  90
                                ]);

                    return redirect("/dashboard")->with("message", "success|" . $res['message']);
                }
                else {
                    return redirect()->back()->with("message", "error|" . $res['message']);
                }
            }
            else{
                return redirect()->back()->with("message", "error|Unable to process your request");
            }
        }
        else {
            return redirect()->back()->with("message", "error|Unable to find the agreement");
        }
    }

    //get final udin
    public function getFinalUdin(Request $request) {
        $udin_num   =   $request->udin_num;

        $agreement = Agreement::where('udin_num', $udin_num)->where('status', 90)->first('quotation_id');

        $quotation_id   =   $agreement->quotation_id;

        $udin_resp  =   json_decode(get_udin_by_quotation($quotation_id), true);
        Session::put('udin_random_key', $udin_resp['random_key']);

        if(!is_null($udin_resp)){
            if(!$udin_resp['error']) {

                $udin_number    =   $udin_resp['data'][1]['udin'];

                Agreement::where('quotation_id', $quotation_id)->update([
                    'udin_num'  =>  $udin_number,
                    'status'    =>  99
                ]);

                $reponse = array(
                    'error'     =>  false,
                    'message'   =>  $udin_resp['message']
                );
                return response(json_encode($reponse), 200);
            }
            else {
                $reponse = array(
                    'error'     =>  true,
                    'message'   =>  $udin_resp['message']
                );
                return response(json_encode($reponse), 200);
            }
        }
        else {
            $reponse = array(
                'error'     =>  true,
                'message'   =>  "Unable to process your request"
            );
            return response(json_encode($reponse), 200);
        }
    }

    //view draft agreement
    public function getDraftAgreement($ref_num) {
        $agreement = DB::table('agreement_master')
                        ->where('ref_num', $ref_num)->first();

        $applicant = DB::table('users')->where('user_id', $agreement->applicant_user_id)->first();
        $co_applicant = DB::table('users')->where('user_id', $agreement->co_applicant_user_id)->first();
        $w_1 = DB::table('users')->where('user_id', $agreement->witness_1_user_id)->first();
        $w_2 = DB::table('users')->where('user_id', $agreement->witness_2_user_id)->first();

        $lessor = array();
        $lessee = array();
        $witness_1 = array();
        $witness_2 = array();

        if($agreement->applicant_type == 'lessor') {
            $lessor = array(
                            'lessor_aadhaar'    =>  isset($applicant) ? $applicant->user_aadhaar_num : 'AADHAAR OF LESSOR',
                            'lessor_fullname'   =>  isset($applicant) ? $applicant->user_aadhaar_name : 'FULLNAME OF LESSOR',
                            'lessor_address'    =>  isset($applicant) ? $applicant->user_aadhaar_address : 'ADDRESS OF LESSOR'
                        );

            $lessee = array(
                'lessee_aadhaar'    =>  isset($co_applicant) ? $co_applicant->user_aadhaar_num : 'AADHAAR OF LESSEE',
                'lessee_fullname'   =>  isset($co_applicant) ? $co_applicant->user_aadhaar_name : 'FULLNAME OF LESSEE',
                'lessee_address'    =>  isset($co_applicant) ? $co_applicant->user_aadhaar_address : 'ADDRESS OF LESSEE'
            );
        }
        else {
            $lessor = array(
                'lessor_aadhaar'    =>  isset($co_applicant) ? $co_applicant->user_aadhaar_num : 'AADHAAR OF LESSOR',
                'lessor_fullname'   =>  isset($co_applicant) ? $co_applicant->user_aadhaar_name : 'FULLNAME OF LESSOR',
                'lessor_address'    =>  isset($co_applicant) ? $co_applicant->user_aadhaar_address : 'ADDRESS OF LESSOR'
            );

            $lessee = array(
                'lessee_aadhaar'    =>  isset($applicant) ? $applicant->user_aadhaar_num : 'AADHAAR OF LESSEE',
                'lessee_fullname'   =>  isset($applicant) ? $applicant->user_aadhaar_name : 'FULLNAME OF LESSEE',
                'lessee_address'    =>  isset($applicant) ? $applicant->user_aadhaar_address : 'ADDRESS OF LESSEE'
            );
        }

        $witness_1 = array(
            'witness_1_aadhaar'    =>  isset($w_1) ? $w_1->user_aadhaar_num : 'AADHAAR OF WITNESS ONE',
            'witness_1_fullname'   =>  isset($w_1) ? $w_1->user_aadhaar_name : 'FULLNAME OF WITNESS ONE',
            'witness_1_address'    =>  isset($w_1) ? $w_1->user_aadhaar_address : 'ADDRESS OF WITNESS ONE'
        );

        $witness_2 = array(
            'witness_2_aadhaar'    =>  isset($w_2) ? $w_2->user_aadhaar_num : 'AADHAAR OF WITNESS TWO',
            'witness_2_fullname'   =>  isset($w_2) ? $w_2->user_aadhaar_name : 'FULLNAME OF WITNESS TWO',
            'witness_2_address'    =>  isset($w_2) ? $w_2->user_aadhaar_address : 'ADDRESS OF WITNESS TWO'
        );

        $pdf_data   =   array();

        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ]);

        $estamp_img_url =   env('ESTAMP_URL') . "api/estamp/" . $agreement->estamp_num;

        $estamp_data    =   array(
            'estamp_num'    =>  $agreement->estamp_num,
            'estamp_url'    =>  env('ESTAMP_URL'),
            'estamp_img'    =>  base64_encode(file_get_contents($estamp_img_url, false, $context))
        );

        $property_data      =   $agreement->property_detail;
        $property_address   =   $agreement->property_address . ", " . $agreement->property_city . ", " . $agreement->property_state . ", " . $agreement->property_pin;
        $contract_detail    =   $agreement->contract_detail;

        $pdf_data   =   array(
            'estamp_data'       =>  json_encode($estamp_data),
            'property_data'     =>  $property_data,
            'property_address'  =>  json_encode($property_address),
            'contract_detail'   =>  $contract_detail,
            'lessor'            =>  json_encode($lessor),
            'lessee'            =>  json_encode($lessee),
            'witness_1'         =>  json_encode($witness_1),
            'witness_2'         =>  json_encode($witness_2),
        );

        //print_r($pdf_data);exit();

        $pdf = PDF::set_option('isRemoteEnabled', true)->loadView('pdf.rent_agreement', $pdf_data);

        return $pdf->download('rent_agreement.pdf');
    }

    //download udin doc
    public function downloadUdinDocument(Request $request) {
        $udin_num   =   $request->udin_num;

        $udin_resp  =   json_decode(get_udin_details($udin_num), true);

        if(!$udin_resp['error']) {
            $reponse = array(
                'doc_name'              =>  $udin_num,
                'doc_name_original'     =>  $udin_resp['document']['document_data']['doc_name'],
                'doc_base64'            =>  $udin_resp['document']['document_data']['doc_base64'],
                'doc_base64_original'   =>  $udin_resp['document']['document_data']['doc_original_base64']
            );
            return response(json_encode($reponse), 200);
        }
        else {
            $reponse = array(
                'error'     =>  true,
                'message'   =>  $udin_resp['message']
            );
            return response(json_encode($reponse), 200);
        }

    }

    //get agreement data
    public function getAgreementData(Request $request, $ref_num) {
        $agreement = DB::table('agreement_master')
                        ->where('ref_num', $ref_num)->first();

        $applicant = DB::table('users')->where('user_id', $agreement->applicant_user_id)->first();
        $co_applicant = DB::table('users')->where('user_id', $agreement->co_applicant_user_id)->first();
        $w_1 = DB::table('users')->where('user_id', $agreement->witness_1_user_id)->first();
        $w_2 = DB::table('users')->where('user_id', $agreement->witness_2_user_id)->first();

        $lessor = array();
        $lessee = array();
        $witness_1 = array();
        $witness_2 = array();

        if($agreement->applicant_type == 'lessor') {
            $lessor = array(
                            'lessor_fullname'   =>  isset($applicant) ? $applicant->user_aadhaar_name : 'FULLNAME OF LESSOR',
                            'lessor_address'    =>  isset($applicant) ? $applicant->user_aadhaar_address : 'ADDRESS OF LESSOR'
                        );

            $lessee = array(
                'lessee_fullname'   =>  isset($co_applicant) ? $co_applicant->user_aadhaar_name : 'FULLNAME OF LESSEE',
                'lessee_address'    =>  isset($co_applicant) ? $co_applicant->user_aadhaar_address : 'ADDRESS OF LESSEE'
            );
        }
        else {
            $lessor = array(
                'lessor_fullname'   =>  isset($co_applicant) ? $co_applicant->user_aadhaar_name : 'FULLNAME OF LESSOR',
                'lessor_address'    =>  isset($co_applicant) ? $co_applicant->user_aadhaar_address : 'ADDRESS OF LESSOR'
            );

            $lessee = array(
                'lessee_fullname'   =>  isset($applicant) ? $applicant->user_aadhaar_name : 'FULLNAME OF LESSEE',
                'lessee_address'    =>  isset($applicant) ? $applicant->user_aadhaar_address : 'ADDRESS OF LESSEE'
            );
        }

        $witness_1 = array(
            'witness_1_fullname'   =>  isset($w_1) ? $w_1->user_aadhaar_name : 'FULLNAME OF WITNESS ONE',
            'witness_1_address'    =>  isset($w_1) ? $w_1->user_aadhaar_address : 'ADDRESS OF WITNESS ONE'
        );

        $witness_2 = array(
            'witness_2_fullname'   =>  isset($w_2) ? $w_2->user_aadhaar_name : 'FULLNAME OF WITNESS TWO',
            'witness_2_address'    =>  isset($w_2) ? $w_2->user_aadhaar_address : 'ADDRESS OF WITNESS TWO'
        );

        $pdf_data   =   array();

        $estamp_data    =   array(
            'estamp_num'    =>  $agreement->estamp_num,
            'estamp_amt'    =>  $agreement->estamp_amt,
            'estamp_date'   =>  $agreement->estamp_date
        );

        $property_data      =   $agreement->property_detail;
        $property_address   =   $agreement->property_address . ", " . $agreement->property_city . ", " . $agreement->property_state . ", " . $agreement->property_pin;
        $contract_detail    =   $agreement->contract_detail;

        $agr_data   =   array(
            'estamp_data'       =>  json_encode($estamp_data),
            'property_data'     =>  $property_data,
            'property_address'  =>  json_encode($property_address),
            'contract_detail'   =>  $contract_detail,
            'lessor'   =>  json_encode($lessor),
            'lessee'   =>  json_encode($lessee),
            'witness_1'   =>  json_encode($witness_1),
            'witness_2'   =>  json_encode($witness_2),
        );

        print_r(json_decode(json_encode($agr_data)));
    }

}
