@extends('layouts.default')
@section('page_title', 'Authenticate yourself')
@section('content')

<div class="col-9">
    {{ Form::open(['url' => '/user-authenticate', 'method' => 'post', 'class' => '']) }}  
        <div class="card" id="card_1">
            <div class="card-header">
                <h2>Authenticate with UDIN</h2>
            </div>
            <div class="card-body" id="login_request">
                <div class="row mb-3">
                    <div class="col-lg-8 col-md-8">
                        <label for="mobile_num">Mobile Number <span class="required">*</span></label>
                        <input type="text" class="form-control number" id="mobile_num" name="mobile_num" maxlength="10" minlength="10" autocomplete="off" inputmode="numeric" title="Enter 10 digit mobile number" required="">
                    </div>

                    <div class="col-lg-4 col-md-4 text-end">
                        <div class="mt-2">&nbsp;</div>
                        <button type="button" class="btn btn-primary btn-login">Get OTP</button>
                    </div>

                    
                </div>

                <div class="row mb-3">
                    <div class="col-lg-12">
                        <p>Not a registered UDIN user? <a href="/user-registration">Register here.</a></p>
                    </div>    
                </div>
            </div>

            <div class="card-body hide" id="otp_request">
                <div class="row mb-3">
                    <div class="col-lg-8 col-md-8">
                        <label for="otp">One Time Password <span class="required">*</span></label>
                        <input type="text" class="form-control number" id="login_otp_num" name="login_otp_num" maxlength="6" minlength="6" autocomplete="off" inputmode="numeric" title="Enter One Time Password" required>
                    </div>
                    <div class="col-lg-4 col-md-4 text-end">
                        <div class="mt-2">&nbsp;</div>
                        <button type="button" class="btn btn-primary btn-verify-login-otp">Verify OTP</button>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-lg-12">
                        <p>Not a registered UDIN user? <a href="/user-registration">Register here.</a></p>
                    </div>    
                </div>
            </div>
        </div>
    </form>
</div>                        

@stop