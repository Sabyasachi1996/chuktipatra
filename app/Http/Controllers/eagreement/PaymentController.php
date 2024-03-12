<?php

namespace App\Http\Controllers\eagreement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

use App\Models\eagreement\Payment;
use App\Models\eagreement\PaymentDetail;

use Exception;
use Validator;
use DB;
use Session;
use DateTime;

class PaymentController extends Controller {

    protected $auth;
    public $back_url = null;

    public function __construct() {
        //$this->auth = new Authentication();
    }

    public function doPayment(Request $request) {
        $now    =   date('Y-m-d H:i:s');
        $today  =   date('Y-m-d');
        $user_id    =   0;

        $escr       =   $request->escr;
        $esrd       =   json_decode(base64_decode($request->esrd));

        //get form data
        $fullname   =   strtoupper($esrd->fullname);
        $address    =   strtoupper($esrd->address);
        $phone      =   $esrd->phone;
        $email      =   strtolower($esrd->email);
        $purpose    =   strtoupper($esrd->purpose);
        $amount     =   $esrd->amount;
        $ret_url    =   $esrd->return_url;

        $full_address   =   $address;

        $srcId          =   'CHUKTIPATRA';
        $userId         =   'UDIN123';
        $ip             =   $_SERVER['SERVER_ADDR'];
        $mac            =   '52:54:00:4c:37:09';
        $sysTimeStamp   =   date('d/m/Y H:i:s');
        $gripsRespUrl   =   app_url() . 'payment-response';
        $dpr            =   generateUniqueAlphanumericNumber();
        $drn            =   generateUniqueAlphanumericNumber(3);
        $identNo        =   generateUniqueAlphanumericNumber();

        $hoaDtls        =   array(
            "hoa"   =>  "0070-60-501-002-16",
            "amt"   =>  (int)$amount
        );

        $addlParam      =   array(
            "paramName" =>  "Phone Number",
            "paramVal"  =>  $phone
        );

        $paymentDtls    =   array(
            "pmtCategory"       =>  "G",
            "deptCode"          =>  "046",
            "svcCode"           =>  "306",
            "drn"               =>  $drn,
            "identificationNo"  =>  $identNo,
            "depName"           =>  $fullname,
            "depAddress"        =>  $full_address,
            "depEmail"          =>  $email,
            "onBehalfOf"        =>  $fullname,
            "depType"           =>  "Citizen",
            "periodFrom"        =>  formatDate($today, 'Y-m-d', 'd/m/Y'),
            "periodTo"          =>  formatDate($today, 'Y-m-d', 'd/m/Y'),
            "remarks"           =>  $purpose,
            "totalAmt"          =>  (int)$amount,

            "hoaDtls"           =>  array($hoaDtls),
            "additionalParamDtls"   =>  array($addlParam),
        );

        $req_data   =   array(
            "dpr"           =>  $dpr,
            "src"           =>  $srcId,
            "respUrl"       =>  $gripsRespUrl,
            "action"        =>  "PMT",
            "paymentAmt"    =>  (int)$amount,
            "depMob"        =>  $phone,
            "paymentDtls"   =>  array($paymentDtls)
        );

        $src_data   =   array(
            "srcId"         =>  $srcId,
            "userId"        =>  $userId,
            "ip"            =>  $ip,
            "mac"           =>  $mac,
            "sysTimeStamp"  =>  $sysTimeStamp
        );

        $payload    =   array(
            "req"   =>  $req_data,
            "src"   =>  $src_data
        );

        $json_request_encode    =   json_encode($payload);

        $encData    =   gripsEncrypt($json_request_encode);
        $csRequest  =   hash256Checksum($json_request_encode);

        //save to DB
        DB::beginTransaction();

        try {

            $payment_data   =   array(
                'clientRefNum'  =>  $dpr,
                'clientReqId'   =>  $escr,
                'clientReturnUrl'   =>  $ret_url,
                'dpr'           =>  $dpr,
                'src'           =>  $srcId,
                'respUrl'       =>  $gripsRespUrl,
                'action'        =>  "PMT",
                'paymentAmt'    =>  (int)$amount,
                'depMob'        =>  $phone,
                'srcId'         =>  $srcId,
                'userId'        =>  $userId,
                'ip'            =>  $ip,
                'mac'           =>  $mac,
                'sysTimeStamp'  =>  $sysTimeStamp,
                'pmtRequest'    =>  $json_request_encode,
                'encDataRequest'=>  $encData,
                'csRequest'     =>  $csRequest,
                'created_by'    =>  $user_id,
                'created_on'    =>  $now
            );

            Payment::insert($payment_data);
            $pmt_id = DB::getPdo()->lastInsertId();

            $pmt_dtl_data = array(
                'pmt_id'            =>  $pmt_id,
                'pmtCategory'       =>  $paymentDtls['pmtCategory'],
                'deptCode'          =>  $paymentDtls['deptCode'],
                'svcCode'           =>  $paymentDtls['svcCode'],
                'drn'               =>  $paymentDtls['drn'],
                'identificationNo'  =>  $paymentDtls['identificationNo'],
                'depName'           =>  $paymentDtls['depName'],
                'depAddress'        =>  $paymentDtls['depAddress'],
                'depEmail'          =>  $paymentDtls['depEmail'],
                'onBehalfOf'        =>  $paymentDtls['onBehalfOf'],
                'depType'           =>  $paymentDtls['depType'],
                'periodFrom'        =>  formatDate($paymentDtls['periodFrom'], 'd/m/Y', 'Y-m-d'),
                'periodTo'          =>  formatDate($paymentDtls['periodTo'], 'd/m/Y', 'Y-m-d'),
                'remarks'           =>  $paymentDtls['remarks'],
                'totalAmt'          =>  $paymentDtls['totalAmt'],
                'paramDtls'         =>  json_encode($paymentDtls['additionalParamDtls'])
            );

            PaymentDetail::insert($pmt_dtl_data);
            $pmt_dtl_id = DB::getPdo()->lastInsertId();

            if($pmt_dtl_id > 0) {
                DB::commit();

                return view('pages.payment.payment_redirect', [
                    "paymentrequest"    =>  "https://uat.wbifms.gov.in/GRIPS/epayRevG2.do",
                    "encData"           =>  $encData,
                    "cs"                =>  $csRequest,
                    "src"               =>  $srcId
                ]);
            }
            else {
                DB::rollback();
                //echo "ELSE"; exit();
                return redirect()->back()->with(
                    array(
                        'error'     =>  true,
                        'message'   =>  "Unable to process your request"
                    )
                );
            }
        }
        catch(Exception $e) {
            DB::rollback();
            return redirect()->back()->with(
                array(
                    'error'     =>  true,
                    'message'   =>  $e->getMessage()
                )
            );
        }
    }

    public function getPaymentResponse(Request $request) {
        $now        =   date('Y-m-d H:i:s');
        $encDataReq =   $request->input('encData');
        $csReq      =   $request->input('cs');
        $srcReq     =   $request->input('src');

        $txtData    =   gripsDecrypt($encDataReq);
        $cs         =   hash256Checksum($txtData);

        $grips_res   =   json_decode($txtData, true);

        if ( ($csReq == $cs) && is_array($grips_res) && !empty($grips_res) ) {
            try {
                DB::beginTransaction();

                $dpr            =   $grips_res['res']['dpr'];
                $payment        =   Payment::where('dpr', $dpr)->first();
                $redirect_url   =   $payment->respUrl;
                $clientRefNum   =   $payment->clientRefNum;

                $grips_res['res']['dpr'] = $clientRefNum;

                $payment_data    =   array(
                    'gpr'               =>  $grips_res['res']['gpr'],
                    'paymentStatus'     =>  $grips_res['res']['paymentStatus'],
                    'paymentStatusDesc' =>  $grips_res['res']['paymentStatusDesc'],
                    'pmtResponse'       =>  $grips_res['res']['dpr'],
                    'encDataResponse'   =>  $encDataReq,
                    'csResponse'        =>  $csReq,
                    'response_on'       =>  $now,
                );

                Payment::where('dpr', $dpr)->update($payment_data);

                $paymentDtls = $grips_res['res']['paymentDtls'];

                if (is_array($paymentDtls) && !empty($paymentDtls)) {
                    foreach($paymentDtls as $pmtDtl) {
                        $payment_dtl_data    =   array(
                            'grn'           =>  $pmtDtl['grn'],
                            'grnTime'       =>  formatDate($pmtDtl['grnTime'], 'd/m/Y H:i:s', 'Y-m-d H:i:s'),
                            'grnStatus'     =>  $pmtDtl['grnStatus'],
                            'grnStatusDesc' =>  $pmtDtl['grnStatusDesc'],
                            'bank'          =>  $pmtDtl['bank'],
                            'paymentMode'   =>  $pmtDtl['paymentMode'],
                            'brn'           =>  $pmtDtl['brn'],
                            'brnTime'       =>  formatDate($pmtDtl['brnTime'], 'd/m/Y H:i:s', 'Y-m-d H:i:s'),
                            'gatewayRefId'  =>  $pmtDtl['gatewayRefId']
                        );

                        PaymentDetail::where('drn', $pmtDtl['drn'])
                            ->where('identificationNo', $pmtDtl['identificationNo'])->update($payment_dtl_data);
                    }
                }

                DB::commit();

                return redirect('get-payment/' . $clientRefNum);

            }
            catch(Exception $e) {
                DB::rollBack();

                return redirect('/payment/error')->with(
                    array(
                        'error'     =>  true,
                        'message'   =>  'There is some error'
                    )
                );
            }
        }
        else {

        }
    }

    public function getPayment(Request $request, $ref_num) {
        $payment = Payment::where('clientRefNum', $ref_num)->first();

        $redirect_url   =   $payment->respUrl; //app_url() . 'show-payment';
        $encDataReq     =   $payment->encDataResponse;
        $clientReqId    =   $payment->clientReqId;

        $txtData    =   gripsDecrypt($encDataReq);
        $grips_res  =   json_decode($txtData, true);

        $grips_res['res']['clientRefNum']   =   $ref_num;

        unset($grips_res['res']['src']);
        unset($grips_res['sts']);
        unset($grips_res['src']);

        return view('pages.payment.payment_response', [
            'response_data' =>  json_encode($grips_res),
            'return_url'    =>  $payment->clientReturnUrl,
            'clientReqId'   =>  $clientReqId
        ]);

    }



}
