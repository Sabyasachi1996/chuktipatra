<?php

namespace App\Http\Controllers\eagreement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/* use App\Models\eagreement\Token;
use App\Models\eagreement\Payment;
use App\Models\eagreement\PaymentDetail;
use App\Models\eagreement\PaymentHoa; */

use Exception;
use Validator;
use DB;
use DateTime;

class PageController extends Controller {

    protected $auth;
    public $back_url = null;

    public function __construct() {
        //$this->auth = new Authentication();
    }


    public function showHomepage() {
        return view('pages.homepage', []);
    }


    //step 1
    public function showStep01() {
        return view('pages.step-01', []);
    }

    //step 2
    public function showStep02(Request $request) {
        return view('pages.step-02', []);
    }

    //step 3
    public function showStep03(Request $request) {
        //print_r($request->all());

        if($request->applicant_type == 'individual') {
            return view('pages.step-03-individual', [
                'lessor_lessee'     =>  $request->lessor_lessee,
                'applicant_type'    =>  $request->applicant_type,
            ]);
        }
        else {
            return view('pages.step-03-non-individual', [
                'lessor_lessee'     =>  $request->lessor_lessee,
                'applicant_type'    =>  $request->applicant_type,
            ]);
        }

    }

    //step 4
    public function showStep04(Request $request) {
        if($request->applicant_type == 'individual') {
            return view('pages.step-04-individual', [
                'lessor_lessee'     =>  $request->lessor_lessee,
                'applicant_type'    =>  $request->applicant_type,
            ]);
        }
        else {
            return view('pages.step-04-non-individual', [
                'lessor_lessee'     =>  $request->lessor_lessee,
                'applicant_type'    =>  $request->applicant_type,
            ]);
        }
    }

    //show buy stamp paper
    public function showBuyStampPaper() {
        return view('pages.buypage', []);
    }

    //check SMS
    public function showSms() {
        return view('pages.showsms', []);
    }

    //validate SMS
    public function validateSms(Request $request) {
        $phone_number   =   $request->phone;
        $template       =   $request->template;
        $sms_message    =   $request->message;

        $status = '';

        if( ($phone_number != '') && ($template != '') && ($sms_message != '') ) {
            // dd($phone_number."   ".$template."  ".$sms_message);
            $status =   initiateSmsActivation($phone_number, $sms_message, $template);
        }

        return redirect()->back()->with("message", "success|$status");
    }
}
