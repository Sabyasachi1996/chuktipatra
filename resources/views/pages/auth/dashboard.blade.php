@extends('layouts.default')
@section('page_title', 'Dashboard')
@section('content')


<div class="col-lg-12 col-md-12 mb-3 text-end">   
    <a href="/basic-details" class="btn btn-primary">Generate New Agreement</a> 
</div>

<div class="col-lg-12 col-md-12">

    {{-- AGREEMENTS AS APPLICANT --}}
    
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2>Your eAgreement(s)</h2>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-lg-12">
                    @if(!$agreements->isEmpty())
                    <div class="table-responsive">
                        <table class="table table-border table-condense">
                            <tr>
                                <th>Agreement #</th>
                                <th>Role</th>
                                <th>UDIN #</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            @foreach($agreements as $a)
                                <tr>
                                    <td>
                                        @if($a->status == -1)
                                            {{$a->ref_num}}
                                        @else 
                                            @if($a->status < 99)
                                                <a href="/draft-agreement/{{$a->ref_num}}" target="_blank">{{$a->ref_num}}</a>
                                            @else 
                                                {{$a->ref_num}}
                                            @endif
                                            <br>
                                            <small>{{ formatDate($a->estamp_date, 'Y-m-d H:i:s', 'd F, Y') }}</small>
                                        @endif
                                        
                                    </td>
                                    <td>
                                        @if($a->applicant_user_id == Session::get('user_id'))
                                            {{ ucwords($a->applicant_type) }}
                                        @elseif($a->co_applicant_user_id == Session::get('user_id')) 
                                            {{ ucwords($a->co_applicant_type) }}
                                        @else 
                                            Witness    
                                        @endif
                                    </td>
                                    <td>
                                        {!! 
                                            $a->udin_num == '' ? 
                                                "<span class='badge bg-danger'>Not Generated</span>" : 
                                                (($a->status < 99) ? $a->udin_num . "<br><small>(Provisional UDIN)</small>" : $a->udin_num)
                                                
                                        !!}
                                    </td>
                                    
                                    <td>
                                        @if($a->status == -1)
                                            <span class="badge bg-danger">Payment failed</span>
                                        @elseif($a->status == 0)
                                            <span class="badge bg-secondary">Request to Co-applicant pending</span>
                                        @elseif($a->status == 1)
                                            <span class="badge bg-primary">Request sent to Co-applicant</span>
                                        @elseif($a->status == 2)    
                                            <span class="badge bg-primary">Co-applicant accepted</span>
                                        @elseif($a->status == 3)
                                            <span class="badge bg-primary">Request sent to Witness</span>
                                        @elseif($a->status == 4)
                                            <span class="badge bg-warning">One of Witnesses signed</span>
                                        @elseif($a->status == 5)
                                            <span class="badge bg-secondary">Upload pending</span>
                                        @elseif($a->status == 6)
                                            <span class="badge bg-warning">Provisional UDIN generated</span> 
                                        @elseif($a->status == 7)
                                            <span class="badge bg-primary">Signing Request sent to Co-applicant</span>
                                        @elseif($a->status == 8)
                                            <span class="badge bg-primary">Co-applicant signed</span>
                                        @elseif($a->status == 9)    
                                            <span class="badge bg-primary">Signing Request sent to Witness One</span>  
                                        @elseif($a->status == 10)
                                            <span class="badge bg-primary">Witness One signed</span>
                                        @elseif($a->status == 11)
                                            <span class="badge bg-primary">Signing Request sent to Witness Two</span>
                                        @elseif($a->status == 12)
                                            <span class="badge bg-primary">All signing completed</span>
                                        @elseif($a->status == 90)
                                            <span class="badge bg-primary">UDIN generation in-progress</span>
                                        @elseif($a->status == 99)
                                            <span class="badge bg-success">UDIN generated</span>
                                        @else 
                                            &nbsp;    
                                        @endif
                                    </td>
                                    
                                    <td>
                                        @if( ($a->status == 0) && ($a->applicant_user_id == Session::get('user_id')) )
                                            <a href="#" class='btn btn-primary show-cosigner-modal' data-agreement="{{$a->ref_num}}" data-title="@if($a->applicant_type == 'lessor') Lessee @else Lessor @endif">@if($a->applicant_type == 'lessor') Add Lessee @else Add Lessor @endif</a>

                                        @elseif(($a->status == 2) && ($a->applicant_user_id == Session::get('user_id')))    
                                            <a href="#" class='btn btn-primary show-witness-modal' data-agreement="{{$a->ref_num}}">Add Witness</a>
                                        
                                        @elseif( ($a->status == 5) && ($a->applicant_user_id == Session::get('user_id')))
                                            <a href="/upload-document/{{$a->ref_num}}" class='btn btn-primary'>Upload Document</a> 

                                        @elseif( ($a->status == 6) && ($a->applicant_user_id == Session::get('user_id')))
                                            <a href="/co-app-sign-request/{{$a->ref_num}}" class='btn btn-primary'>Request Co-Applicant to Sign</a>
                                        
                                        @elseif( ($a->status == 8) && ($a->applicant_user_id == Session::get('user_id')))
                                            <a href="/witness-sign-request/1/{{$a->ref_num}}" class='btn btn-primary'>Request Witness One to Sign</a>
                                            
                                        @elseif( ($a->status == 10) && ($a->applicant_user_id == Session::get('user_id')))
                                            <a href="/witness-sign-request/2/{{$a->ref_num}}" class='btn btn-primary'>Request Witness Two to Sign</a>
                                        
                                        @elseif( ($a->status == 12) && ($a->applicant_user_id == Session::get('user_id')))
                                            {{ Form::open(['url' => '/generate-final-document', 'method' => 'post', 'class' => '']) }}
                                                <input type="hidden" name="ref_num" value="{{$a->ref_num}}">
                                                <button type="submit" class="btn btn-primary">Generate Final Agreement</button>
                                            </form>
                                        @elseif( ($a->status == 90) )
                                            <a href="javascript:void(0)" class="btn btn-primary btn-get-udin" data-udin="{{$a->udin_num}}">Get Final UDIN</a>
                                        @elseif( ($a->status == 99) )
                                            <a href="javascript:void(0)" class="btn btn-success btn-download-udin" data-udin="{{$a->udin_num}}"><i class="las la-file-download"></i> Download</a>
                                        @endif    
                                    </td>    
                                </tr>
                            @endforeach
                        </table>
                    </div>  
                    @else 
                    <div class="text-danger">No data available</div>  
                    @endif
                </div>
            </div>
        </div>
    </div>
    
</div>     

<!-- Co-signer Modal -->
<div class="modal fade" id="cosignerModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="cosignerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            {{ Form::open(['url' => '/co-app-request', 'method' => 'post', 'class' => '']) }}
                <input type="hidden" name="agreement_id" id="agreement_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="cosignerModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-lg-8 col-md-8">
                            <label for="mobile_num">Mobile Number <span class="required">*</span></label>
                            <input type="text" class="form-control number" id="mobile_num" name="mobile_num" maxlength="10" minlength="10" autocomplete="off" inputmode="numeric" title="Enter 10 digit mobile number" required="">
                        </div>

                        <div class="col-lg-4 col-md-4 text-end">
                            <div class="mt-2">&nbsp;</div>
                            <button type="button" class="btn btn-primary btn-coapp-request">Send Request</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            
            </form>
        </div>
    </div>
</div>

<!-- Witness Modal -->
<div class="modal fade" id="witnessModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="witnessModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            {{ Form::open(['url' => '/witness-request', 'method' => 'post', 'class' => '']) }}
                <input type="hidden" name="agreement_id2" id="agreement_id2">
                <div class="modal-header">
                    <h5 class="modal-title" id="witnessModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-lg-8 col-md-8">
                            <label for="mobile_num1">Mobile Number of Witness One<span class="required">*</span></label>
                            <input type="text" class="form-control number" id="mobile_num1" name="mobile_num1" maxlength="10" minlength="10" autocomplete="off" inputmode="numeric" title="Enter 10 digit mobile number" required="">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-lg-8 col-md-8">
                            <label for="mobile_num2">Mobile Number of Witness Two<span class="required">*</span></label>
                            <input type="text" class="form-control number" id="mobile_num2" name="mobile_num2" maxlength="10" minlength="10" autocomplete="off" inputmode="numeric" title="Enter 10 digit mobile number" required="">
                        </div>

                        <div class="col-lg-4 col-md-4 text-end">
                            <div class="mt-2">&nbsp;</div>
                            <button type="button" class="btn btn-primary btn-witness-request">Send Request</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            
            </form>
        </div>
    </div>
</div>

<!-- Upload Doc Modal -->
<div class="modal fade" id="uploadModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="cosignerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            {{ Form::open(['url' => '/upload-doc', 'method' => 'post', 'class' => '']) }}
                <input type="hidden" name="upload_agreement_id" id="upload_agreement_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadModalLabel">Authenticate with Aadhaar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
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
                                <button type="button" class="btn btn-primary btn-get-upload-aadhaar-otp">Get OTP</button>
                            </div>
                            <div class="col-lg-12">
                                <small class="hint">eAgreement uses Aadhaar for verification of the user.</small>
                            </div>
                        </div>
                    </div>  
                    
                    <div class="hide" id="otp_request">
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
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            
            </form>
        </div>
    </div>
</div>

@stop