@extends('layouts.default')
@section('page_title', 'Other party')
@section('content')

<div class="col-9">
    {{ Form::open(['url' => '/save-other-party', 'method' => 'post', 'class' => '']) }}  
        <div class="card" id="card_1">
            <div class="card-header">
                <h2>Other party intimation</h2>
            </div>

            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-lg-4">
                        <label for="applicant2_phone">Phone Number</label>
                        <input name="applicant2_phone" placeholder="" autocomplete="off" type="text" id="applicant2_phone" class="form-control number" value="" maxlength="10">
                    </div>
                    <div class="col-lg-12">
                        <p>You need to provide the phone number of the {{ ucwords($co_applicant_type) }}. 
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-lg-12 text-end">
                        <button type="button" class="form-btn btn_next_payment" role="button">Next</button>
                    </div>
                </div>
            </div>
        
        </div>    

    </form>
</div>                        
    
@stop            