@extends('layouts.app')

@section('content')
<section class="container-fluid ">
    <div class="row justify-content-evenly  ">
        <div class="col-md-6  bg-warning bg-opacity-10 rounded-5 ">
            <canvas id="chart1"></canvas>
        </div>
        <div class="col-md-6">
            <canvas id="chart2"></canvas>
        </div>
    </div>
</section>
@endsection