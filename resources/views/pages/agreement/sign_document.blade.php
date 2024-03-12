@extends('layouts.default')
@section('page_title', 'Sign Agreement')
@section('content')

<div class="col-9">
    {{ Form::open(['url' => '/upload-doc', 'method' => 'post', 'class' => '']) }}

    <div class="card">
        <div class="card-header">
            <h2>Sign Agreement with Aadhaar</h2>
        </div>

        <div class="card-body">
            <div class="row mb-3">
                <div class="col-lg-4 col-md-4">
                    <label for="mobile_num">Phone Number <span class="required">*</span></label>
                    <input type="text" class="form-control number" id="mobile_num" name="mobile_num" maxlength="10" minlength="10" autocomplete="off" inputmode="numeric" title="Enter 10 digit mobile number" required="">
                </div>
                <div class="col-lg-6 col-md-6">
                    <label for="udin_num">UDIN Number <span class="required">*</span></label>
                    <input type="text" class="form-control" id="udin_num" name="udin_num" maxlength="50" minlength="50" autocomplete="off" title="Enter 25 digit UDIN number" required="">
                </div>
                <div class="col-lg-2 col-md-2 text-end">
                    <div class="mt-2">&nbsp;</div>
                    <button type="button" class="btn btn-primary btn-get-sign-doc-otp">Get OTP</button>
                </div>
            </div>


            <div id="otp_request" class="hide">
                <div class="row mb-3">
                    <div class="col-lg-4 col-md-4">
                        <label for="otp_num">One Time Password <span class="required">*</span></label>
                        <input type="text" class="form-control number" id="otp_num" name="otp_num" maxlength="6" minlength="6" autocomplete="off" inputmode="numeric" title="Enter One Time Password" required>
                    </div>
                   
                    <div class="col-lg-2 col-md-2 text-end">
                        <div class="mt-2">&nbsp;</div>
                        <button type="button" class="btn btn-primary btn-verify-sign-doc-otp">Verify OTP</button>
                    </div>
                </div>
            </div>    
        </div>

    </form>
</div>     


<!-- PDF View Doc Modal -->
<div class="modal fade" id="cosignPdfViewerModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="cosignerModalLabel" aria-hidden="true">
    <div class="modal-xl modal-dialog modal-dialog-centered">
        <div class="modal-content">
            {{ Form::open(['url' => '/upload-doc-sign', 'method' => 'post', 'class' => '']) }}
                <input type="hidden" name="modal_udin_num" id="modal_udin_num">
                <input type="hidden" name="modal_phone_num" id="modal_phone_num">
                <input type="hidden" name="modal_trans_id" id="modal_trans_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="cosignPdfViewerModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="owner_name"></div>
                    <div id="owner_statement"></div>

                    <div id="pdf_content" class="mt-1"></div>

                    <div id="signer_message" class="mt-1"><small>As the signer of this document, by the completion of this verification process,it will be considered that you accept the following statement as your own regarding this document:</small></div>

                    <div id="signer_aadhaar" class="mt-1">
                        <div id="aadhaar_request">
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
                                    <button type="button" class="btn btn-primary btn-get-sign-aadhaar-otp">Get OTP</button>
                                </div>
                            </div>
                        </div>
            
                        <div class="hide" id="aadhaar_otp_request">
                            <div class="row mb-3">
                                <div class="col-lg-8 col-md-8">
                                    <label for="otp">One Time Password <span class="required">*</span></label>
                                    <input type="text" class="form-control number" id="aadhaar_otp_num" name="aadhaar_otp_num" maxlength="6" minlength="6" autocomplete="off" inputmode="numeric" title="Enter One Time Password" required>
                                </div>
                                <div class="col-lg-4 col-md-4 text-end">
                                    <div class="mt-2">&nbsp;</div>
                                    <button type="button" class="btn btn-primary btn-verify-sign-aadhaar-otp">Verify OTP</button>
                                </div>
                            </div>
                        </div> 
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>

@stop    