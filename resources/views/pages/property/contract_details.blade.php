@extends('layouts.default')
@section('page_title', 'Contract details')
@section('content')

<div class="col-9">
    {{ Form::open(['url' => '/save-contract-detail', 'method' => 'post', 'class' => '']) }}  
        <input type="hidden" name="ref_num" id="ref_num" value="{{ Session::get('agreement_ref_num') }}">
        <div class="card" id="card_1">
            <div class="card-header">
                <h2>Contract details</h2>
            </div>

            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="agreement_start">Agreement Start Date</label>
                        <input type="date" id="agreement_start" name="agreement_start" class="form-control" maxlength="100">
                    </div>
                    <div class="col-md-4">
                        <label for="agreement_duraion">Agreement Duration</label>
                        <select id="agreement_duraion" name="agreement_duraion" class="form-select">
                            <option value="1 MONTH">1 Month</option>
                            @for($i=2; $i<=60; $i++)
                            <option value="{{$i}} MONTHS">{{$i}} Months</option>
                            @endfor
                        </select>    
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="rent_pay_day">Rent Payment Date</label>
                        <select id="rent_pay_day" name="rent_pay_day" class="form-select">
                            @for($i=1; $i<=31; $i++)
                            <option value="{{$i}}">{{$i . date("S", mktime(0, 0, 0, 0, $i, 0))}} of every month</option>
                            @endfor
                        </select>  
                    </div>

                    <div class="col-md-4">
                        <label for="rent_amount">Monthly Rent Amount</label>
                        <input type="text" id="rent_amount" name="rent_amount" class="form-control number" maxlength="100"> 
                    </div>

                    <div class="col-md-4">
                        <label for="maintenance_amount">Maintenance Charges</label>
                        <input type="text" id="maintenance_amount" name="maintenance_amount" class="form-control number" maxlength="100"> 
                    </div>
                </div>    

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="security_amount">Security Deposit</label>
                        <input type="text" id="security_amount" name="security_amount" class="form-control number" maxlength="100"> 
                    </div>

                    <div class="col-md-4">
                        <label for="notice_period">Notice Period</label>
                        <select id="notice_period" name="notice_period" class="form-select">
                            <option value="1 MONTH">1 Month</option>
                            @for($i=2; $i<=6; $i++)
                            <option value="{{$i}} MONTHS">{{$i}} Months</option>
                            @endfor
                        </select>  
                    </div>
                </div>    

                <div class="row mb-3" id="step2" >
                    <div class="col-lg-12 text-end">
                        <button type="button" class="form-btn btn_next_step4" role="button">Next</button>
                    </div>
                </div>
            </div>
        
        </div>    

    </form>
</div>                        
    
@stop            