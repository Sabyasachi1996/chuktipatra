@extends('layouts/admin')
@section('page_title','Admin Login')
@section('content')
<!-- <div class="card" style="width: 86rem;"> -->
  <!-- <div class="card-body"> -->
      <div class="row title_section pb-3">
          <div class="col-md-12 card-title text-center">
              <h3 class="fw-bolder text-warning">Admin Dashboard</h3>
          </div>
      </div>
      <div class="row adminDashboardItems text-center">
            @foreach($data as $it=>$val)
                 <div class="col-md-4">
                      <div class="card border-warning border-5 shadow-lg  p-3 bg-white rounded" style="width: 18rem;">
                         <div class="card-body">
                                <h5 class="card-title text-bolder text-danger">{{$val->status}}</h5>
                                <p>{{$val->quantity}}</p>
                            </div>
                       </div>
                </div>
            @endforeach
        </div>
      </div>
 <!-- </div> -->
 <!-- </div> -->
@stop