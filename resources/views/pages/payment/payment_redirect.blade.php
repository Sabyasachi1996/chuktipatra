@extends('layouts.default')
@section('page_title', 'eStamp')
@section('content')

<div class="col-9">
    <div class="card" id="card_1">
        <div class="card-header">
            <h2>Re-directing to GRIPS</h2>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-lg-12">
                    <form id="paymentrequest" name="paymentrequest" action="{{ $paymentrequest }}" method="post">
                        <input type="hidden" name="encData" id="encData" value="{{ $encData }}">
                        <input type="hidden" name="cs" id="cs" value="{{ $cs }}">
                        <input type="hidden" name="src" id="src" value="{{ $src }}">
                        <h3 class="text-center text-danger">We will be  redirecting to payment gateway.<br>Please click "Pay Now".</h3>
                        <div class="text-center mt-4"><button type="submit" id="paymentrequest_btn" class="btn btn-primary">Pay via Grips Now</button></div>
                    </form>
                </div>
            </div>
        </div>
    </div>                
</div>

@stop