@extends('layouts.default')
@section('page_title', 'Agreement')
@section('content')

<?php
    $res_data = json_decode($response_data);
?>

<div class="col-9">
    <div class="card" id="card_1">
        <div class="card-header">
            <h2>
                @if($res_data->res->paymentStatus == 'S') 
                    <span class="text-success">Success</span>
                @else 
                    <span class="text-danger">Failed</span>
                @endif
            </h2>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-lg-12">
                    <h6 class="text-center fw-bold mb-3">Online Payment of UDIN Fees</h6>
                    <table class="table">
                        <tr>
                            <td>Amount</td>
                            <td>{!! formatAsIndianCurrency($res_data->res->paymentAmt) !!}</td>
                        </tr>
                        <tr>
                            <td>Client Ref. No.</td>
                            <td>{{ $res_data->res->clientRefNum }}</td>
                        </tr>
                        <tr>
                            <td>Govt. Ref. No. (GRN)</td>
                            <td>{{ $res_data->res->paymentDtls[0]->grn }}</td>
                        </tr>
                        <tr>
                            <td>Bank Ref. No.</td>
                            <td>{{ $res_data->res->paymentDtls[0]->brn }}</td>
                        </tr>
                        <tr>
                            <td>Transaction Date</td>
                            <td>{{ $res_data->res->paymentDtls[0]->grnTime }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-lg-12 text-center">
                    {{ Form::open(['url' => '/upload-udin', 'method' => 'post', 'class' => '']) }}  
                        <button type="submit" class="btn btn-primary">Go Back</button>
                        <input type="hidden" name="clientReqId" value="{{ $clientReqId }}">
                    </form>
                </div>
            </div>    
        </div>
    </div>                
</div>

@stop