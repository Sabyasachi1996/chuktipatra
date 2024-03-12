@extends('layouts.default')
@section('page_title', 'Register yourself')
@section('content')

<div class="col-9">
    {{ Form::open(['url' => '/register-profile', 'method' => 'post', 'class' => '']) }}  
        <div class="card" id="card_1">
            <div class="card-header">
                <h2>UDIN Registration</h2>
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
                        <button type="button" class="btn btn-primary btn-get-aadhaar-otp">Get OTP</button>
                    </div>
                    <div class="col-lg-8">
                        <small class="hint"><input type="checkbox" class="pt-1 aadhaarChk">
                        I hereby state that I have no objection in authenticating myself on Unique Document Identification Number (UDIN) portal * with Aadhaar based authentication system and *give my consent to providing my Aadhaar number, Biometric and/or One-Time Password (OTP) data for Aadhaar based authentication for the Unique Document Identification Number (UDIN) Portal access. I understand that the Aadhaar number, Biometrics and/ or OTP I provide for authentication shall be used for authenticating my identity and the Department of Information Technology &amp; Electronics Government of West Bengal shall ensure security and confidentiality of my personal identity data provided for the purpose of Aadhaar based authentication.</small>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-lg-12">
                        <p>Already registered at UDIN? <a href="/user-authenticate">Login here.</a></p>
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
                        <button type="button" class="btn btn-primary btn-verify-aadhaar-otp">Verify OTP</button>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-lg-12">
                        <p>Already registered at UDIN? <a href="/user-authenticate">Login here.</a></p>
                    </div>    
                </div>
            </div> 

            <div class="card-body hide" id="profile_request">
                <div class="row mb-3">
                    <div class="col-md-8">
                        <label for="fullname">Full Name (as per Aadhaar)</label>
                        <div id="aadhaar_fullname">Sanjoy C</div>
                    </div>
                </div>
                <div class="row mb-3 ">
                    <div class="col-md-8">
                        <label for="address">Address (as per Aadhaar)</label>
                        <div id="aadhaar_address">Saltlake Kolkata</div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-lg-6 col-md-6">
                        <label for="profile_mobile">Mobile Number <span class="required">*</span></label>
                        <input type="text" class="form-control number" id="profile_mobile" name="profile_mobile" maxlength="10" minlength="10"  autocomplete="off" inputmode="numeric" title="Enter mobile number" required>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="mt-2">&nbsp;</div>
                        <button type="button" class="btn btn-primary btn-profile-save">Register your profile</button>
                    </div>
                </div>
            </div>
            

        </div>
    </form>
</div>                        

@stop