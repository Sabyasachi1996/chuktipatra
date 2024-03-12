@extends('layouts.default')
@section('page_title', 'Property details')
@section('content')

<div class="col-9">
    {{ Form::open(['url' => '/save-property-detail', 'method' => 'post', 'class' => '']) }}
        <div class="card" id="card_1">
            <div class="card-header">
                <h2>Property details</h2>
            </div>

            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-lg-6 col-md-6">
                        <label for="property_type">Type of Property</label>
                        <div class="mt-2">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="property_type" value="R"> Residential
                            </div>
                            {{--<div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="property_type" value="C"> Non-Residential
                            </div>--}}
                        </div>
                    </div>
                </div>

                <div id="R" class="hide">
                    @include('partials.residential')
                </div>

                <div id="C" class="hide">
                    @include('partials.non-residential')
                </div>

                <div class="row mb-3" id="step2" >
                    <div class="col-lg-12 text-end">
                        <button type="button" class="form-btn btn_next_step3" role="button">Next</button>
                    </div>
                </div>
            </div>

        </div>

    </form>
</div>

@stop
