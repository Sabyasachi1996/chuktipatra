@extends('layouts/admin')
@section('page_title','Admin Login')
@section('content')
<div class="card" style="width: 35rem;">
  <div class="card-body">
    <div class="row title_section">
        <div class="col-md-12 card-title text-center">
            <span class="fw-bolder text-dark fs-5">Admin Login</span>
        </div>
    </div>
    <div class="row form_section pb-4 mb-3">
        <div class="col-md-12 card-form text-center">
             <form id="adminLoginForm" autocomplete="off">
                   <div class="form-group adminLoginSt1">
                                 <label for="adminPhone">Enter Phone</label>
                                 <input type="text" class="form-control" id="adminPhone" aria-describedby="adminPhoneHelp" placeholder="Enter phone number">
                                 <small id="adminPhoneHelp" class="form-text text-muted">We'll never share your phone number with anyone else.</small>
                             </div>
                     <div class="form-group adminLoginSt2 hide">
                               <label for="adminOTP">OTP</label>
                               <input type="password" class="form-control" id="adminOTP" placeholder="enter OTP here">
                     </div>
              </form>
        </div>
    </div>
    <div class="row button_section">
        <div class="col-md-12 text-center p-5 pt-0">
        <a class="btn btn-warning btn-admin-login-send-otp">Send OTP</a>
        <a class="btn btn-sm btn-warning btn-admin-login-verify-otp hide">Verify OTP</a>
        </div>
    </div>
  </div>
</div>@stop