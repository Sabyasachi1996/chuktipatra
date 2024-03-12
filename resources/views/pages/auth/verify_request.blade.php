@extends('layouts.default')
@section('page_title', 'Verify Request')
@section('content')

<div class="col-9">
    {{ Form::open(['url' => '/verify-request', 'method' => 'post', 'class' => '']) }}  
        <div class="card" id="card_1">
            <div class="card-header">
                <h2>Verify Request</h2>
            </div>
            <div class="card-body" id="login_request">
                <div class="row mb-3">
                    <div class="col-lg-4 col-md-4">
                        <label for="agreement_id">Agreement Number <span class="required">*</span></label>
                        <input type="text" class="form-control number" id="agreement_id" name="agreement_id" maxlength="13" minlength="13" autocomplete="off" inputmode="numeric" title="Enter agreement id" required="">
                    </div>

                    <div class="col-lg-4 col-md-4">
                        <label for="secret_code">Secret Code <span class="required">*</span></label>
                        <input type="text" class="form-control" id="secret_code" name="secret_code" maxlength="6" minlength="6" autocomplete="off" title="Enter secret code" required="">
                    </div>

                    <div class="col-lg-4 col-md-4">
                        <div class="mt-2">&nbsp;</div>
                        <button type="button" class="btn btn-primary btn-verify-request">Verify</button>
                    </div>
                </div>

            </div>

        </div>
    </form>
</div>


@stop