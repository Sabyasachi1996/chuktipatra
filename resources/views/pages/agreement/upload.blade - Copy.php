@extends('layouts.default')
@section('page_title', 'Upload Agreement')
@section('content')

<div class="col-9">
    @if(!$is_upload_aadhaar_authenticated) 
        {{ Form::open(['url' => '/upload-doc', 'method' => 'post', 'class' => '']) }}
        <input type="hidden" name="upload_agreement_id" id="upload_agreement_id" value="{{$agreement_num}}">

            <div class="card">
                <div class="card-header">
                    <h2>Authenticate with Aadhaar</h2>
                </div>

                <div class="card-body" id="aadhaar_request">
                    <div class="row mb-3">
                        <div class="col-lg-8 col-md-8">
                            <label for="aadhaar_num">Aadhaar Number <span class="required">*</span></label>
                            <div class="aadhaar-input">
                                <input type="text" class="form-control number" id="aadhaar_num" name="aadhaar_num" maxlength="12" minlength="12" autocomplete="off" inputmode="numeric" title="Enter 12 digit Aadhaar number" required="">
                                <img src="/assets/images/aadhaar.png">
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-4 text-end">
                            <div class="mt-2">&nbsp;</div>
                            <button type="button" class="btn btn-primary btn-get-upload-aadhaar-otp">Get OTP</button>
                        </div>
                        <div class="col-lg-12">
                            <small class="hint">eAgreement uses Aadhaar for verification of the user.</small>
                        </div>
                    </div>
                </div>    

                <div class="card-body hide" id="otp_request">
                    <div class="row mb-3">
                        <div class="col-lg-8 col-md-8">
                            <label for="otp">One Time Password <span class="required">*</span></label>
                            <input type="text" class="form-control number" id="otp_num" name="otp_num" maxlength="6" minlength="6" autocomplete="off" inputmode="numeric" title="Enter One Time Password" required>
                        </div>
                        <div class="col-lg-4 col-md-4 text-end">
                            <div class="mt-2">&nbsp;</div>
                            <button type="button" class="btn btn-primary btn-verify-upload-aadhaar-otp">Verify OTP</button>
                        </div>
                    </div>
                </div>    
            </div>
        </form>
    @else 

    <div class="card">
        <div class="card-header">
            <h2>Upload Agreement</h2>
        </div>

        <div class="card-body" id="aadhaar_request">
            <div class="row mb-3">
                <div class="col-lg-8 col-md-8">
                    {{ Form::open(['url' => '/upload-udin', 'method' => 'post', 'class' => '']) }}
                        <input type="hidden" name="agreement_num" value="{{$agreement_num}}">
                        <input type="hidden" name="quotation_amount" value="{{$quotation_amount}}">
                        <input type="hidden" name="quotation_id" value="{{$quotation_id}}">
                        {{-- <input type="hidden" name="transaction_ref" value="{{$transaction_ref}}"> --}}
                        <div class="d-flex align-items-center">
                            <h6 class="me-3">To get the UDIN Number, please click</h6>
                            <button type="submit" class="btn btn-primary btn-upload-agreement">Get Provisional UDIN</button>
                        </div>    
                    </form>
                </div>
            </div>
        </div>
    </div>                

    @endif
</div>        

@stop