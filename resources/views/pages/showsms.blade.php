@extends('layouts.default')
@section('page_title', 'Check SMS')
@section('content')

<div class="col-9">
    {{ Form::open(['url' => '/validate-sms', 'method' => 'post', 'class' => '']) }}  
        <div class="card" id="card_1">
            <div class="card-header">
                <h2>Check SMS</h2>
            </div>

            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-lg-6">
                        <input type="text" class="form-control" name="phone" placeholder="Enter 10 digit number">
                    </div>
                    <div class="col-lg-6">
                        <input type="text" class="form-control" name="template" placeholder="Template Code">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-lg-12">
                        <textarea class="form-control" name="message" placeholder="Enter message"></textarea>
                    </div>
                </div>

                <div class="row mb-3" id="step2" >
                    <div class="col-lg-12 text-end">
                        <button type="submit" class="form-btn" role="button">Validate</button>
                    </div>
                </div>
            </div>
        
        </div>    

    </form>
</div>           

@stop