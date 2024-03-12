@extends('layouts.default')
@section('page_title', 'Purchase eStamp')
@section('content')

<div class="col-9">
    {{ Form::open(['url' => $estamp_url, 'method' => 'post', 'class' => '']) }}  
        <div class="card" id="card_1">
            <div class="card-header">
                <h2>Purchase eStamp</h2>
            </div>

            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-lg-12">
                        <h6 class="mb-3">You need to purchase eStamp of Rs {{$estamp_amount}}</h6 clas:>
                        <button type="submit" class="btn btn-primary">Purchase eStamp</button>
                        <input type="hidden" name="esrd" value="{{$enc_estamp}}">
                        <input type="hidden" name="escr" value="{{$agreement_ref_num}}">
                    </div>
                </div>
            </div>
            
        </div>
    </form>
</div>            

@stop 