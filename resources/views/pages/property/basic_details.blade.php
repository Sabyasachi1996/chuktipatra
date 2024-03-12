@extends('layouts.default')
@section('page_title', 'Basic details')
@section('content')

<div class="col-9">
    {{ Form::open(['url' => '/save-basic-detail', 'method' => 'post', 'class' => '']) }}  
        <div class="card" id="card_1">
            <div class="card-header">
                <h2>Basic details</h2>
            </div>

            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-lg-12">
                        <div class="form-check">
                            <input class="form-check-input lessor" type="radio" name="lessor_lessee" value="lessor">
                            <label class="form-check-label" for="lessor_lessee">
                                I am a Lessor - <small>a person who leases or lets a property to another, a landlord</small>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input lessee" type="radio" name="lessor_lessee" value="lessee">
                            <label class="form-check-label" for="lessor_lessee">
                                I am a Lessee - <small class="fw-normal">a person who holds the lease of a property, a tenant</small>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="row mb-3" id="step2" >
                    <div class="col-lg-12 text-end">
                        <button type="button" class="form-btn btn_next_step2" role="button">Next</button>
                    </div>
                </div>
            </div>
        
        </div>    

    </form>
</div>                        
    
@stop            