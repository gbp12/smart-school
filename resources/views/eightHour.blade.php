@extends('layouts.app')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>

</style>


<div class="container-fluid h-100">
    <div class="row justify-content-evenly h-100">
        <div class="col-5 h-100">
            <h1> {{$verDatos["title"]}}</h1>
            <div style="width: 80%; margin: auto;">
                <canvas id="barChartElectricity"></canvas>
            </div>
        </div>
        <div class="col-5 h-100">
            <h1> {{$verDatos["title"]}}</h1>
            <div style="width: 80%; margin: auto;">
                <canvas id="barChartWater" class="w-100 h-100"></canvas>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    let dataWater = (@json($data['totalWaterConsumo'])).map(Number);
    let labelsWater = @json($data['waterLabels']);


    let ctx = document.getElementById('barChartWater').getContext('2d');
    let myChart = new Chart(ctx, {
        type: 'line',
        data: {

            labels: labelsWater, //Works but its highlighted as an error, FIX?
            datasets: [{
                label: 'l/h',
                data: dataWater,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {

            responsive: true,
            aspectRatio: 1.1,
            scales: {
                y: {
                    ticks: {
                        color: '#666',
                        font: {
                            size: 20,
                            weight: 'bold',

                        }
                    },
                    beginAtZero: true,


                    max: ((Math.max(...dataWater) * 1.2)) //Leaves some space at the top
                },
                x: {
                    ticks: {
                        color: '#666',
                        font: {
                            size: 20,
                            weight: 'bold',

                        }
                    }
                }
            }
        }
    });

    let dataElectricity = (@json($data['totalElectricityConsumo'])).map(Number);
    let labelsElectricity = @json($data['electricityLabels']);

    let ctxE = document.getElementById('barChartElectricity').getContext('2d');
    let myChartE = new Chart(ctxE, {
        type: 'line',
        data: {

            labels: labelsElectricity, //Works but its highlighted as an error, FIX?
            datasets: [{
                label: 'kW/h',
                data: dataElectricity,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {

            responsive: true,
            aspectRatio: 1.1,
            scales: {
                y: {
                    ticks: {
                        color: '#666',
                        font: {
                            size: 20,
                            weight: 'bold',
                        }
                    },
                    beginAtZero: true,


                    max: ((Math.max(...dataElectricity) * 1.2)) //Leaves some space at the top
                },
                x: {
                    ticks: {
                        color: '#666',
                        font: {
                            size: 20,
                            weight: 'bold',

                        }
                    }
                }
            }
        }
    });
</script>
@endsection