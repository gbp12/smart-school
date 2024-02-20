@extends('layouts.app')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    #juan {
        color: red;
    }
</style>

<h1> {{$verDatos["title"]}}</h1>
<h2 id=juan></h2>
<div style="width: 80%; margin: auto;">
    <canvas id="barChart"></canvas>
</div>
<script type="text/javascript">
    var ctx = document.getElementById('barChart').getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'line',
        data: {

            labels: @json($data['waterLabels']), //Works but highlighted as error, FIX
            datasets: [{
                label: 'kW/h',
                data: @json($data['totalWaterConsumo']),
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: false
                }
            }
        }
    });
</script>
@endsection