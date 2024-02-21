document.addEventListener("DOMContentLoaded", function () {
    const ctx = document.getElementById("chart1").getContext("2d");
    const ct2 = document.getElementById("chart2").getContext("2d");

    var currentIndex = 0;

    var array_of_functions = [renderWeeklyView, hola, renderEightHours];

    function callNextFunction() {
    crearAll();
    console.log(currentIndex+" es el index")
        if (currentIndex < array_of_functions.length) {
            array_of_functions[currentIndex](ctx, ct2);
            currentIndex++;
        } else {
            currentIndex = 0;
        }
    }

    callNextFunction();
    

    setInterval(() => {
        callNextFunction();
    }, 3000);
});

function clearAll(){
    clearCanvas("chart1");
    clearCanvas("chart2");
    $(".test").remove();
}

function hola() {
    console.log("hola");
    const title = document.getElementById("viewTitle");
    title.innerHTML = "pene";
}




async function renderEightHours(ctx, ct2){
    const title = document.getElementById("viewTitle");
    title.innerHTML = "Consumo semanal";
    //clearCanvas("chart1");
    //clearCanvas("chart2");
    drawEightHours(ctx, ct2);
    /*clearCanvas("chart2");
    drawWeeklyElectricity(ct2);*/


}

async function drawEightHours(ctx, ct2){
    const EightHoursData = await axios
    .get("/getLastEightHours")
    .catch(function (error) {
        console.error("Error fetching data:", error);
    });
    //const testEight = document.getElementById("chart1")
    let htmlBefore = "<h1 class='test'> Ejemplo 8 horas Elec</h1>";
    let htmlAfter = "<h2 class='test'>Ultima medicion <span>"+EightHoursData.data.lastReadingElectricity+"</span> kW/h</h2> \
    <h3 class='test'>Tomada a las <span>"+EightHoursData.data.lastReadingElectricityDate+"</span></h3>";
    //testEight.insertAdjacentHTML("beforebegin", htmlBefore);
    $(htmlBefore).insertBefore("#chart1");
    $(htmlAfter).insertAfter("#chart1");
    //testEight.insertAdjacentHTML("afterend", htmlAfter);
    let options = {
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
    };

console.log(EightHoursData);
console.log("esa fue la data");
    
   // let ctx = document.getElementById('barChartWater').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {

            labels: EightHoursData.data.electricityLabels, //Works but its highlighted as an error, FIX?
            datasets: [{
                label: 'l/h',
                data: EightHoursData.data.totalElectricityConsumo,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(66, 135, 245, 1)',
                borderWidth: 3
            }]
        },
        options: options
    });

    

    //let ctxE = document.getElementById('barChartElectricity').getContext('2d');
    new Chart(ct2, {
        type: 'line',
        data: {

            labels: EightHoursData.data.waterLabels, //Works but its highlighted as an error, FIX?
            datasets: [{
                label: 'kW/h',
                data: EightHoursData.data.totalWaterConsumo,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(153, 149, 41, 1)',
                borderWidth: 3
            }]
        },
        options: options
    });



}

async function renderWeeklyView(ctx, ct2) {
    const title = document.getElementById("viewTitle");
    title.innerHTML = "Consumo semanal";
    //clearCanvas("chart1");
    drawWeeklyWater(ctx);
    //clearCanvas("chart2");
    drawWeeklyElectricity(ct2);
}

async function drawWeeklyWater(ctx) {
    const weeklyWter = await axios
        .get("/getWeeklyWater")
        .catch(function (error) {
            console.error("Error fetching data:", error);
        });

    var data = {
        labels: [
            "Hace 3 semanas",
            "Hace 2 semanas",
            "Semana pasada",
            "Ultimos 7 dias",
        ],
        datasets: [
            {
                label: "Consumo de agua",
                backgroundColor: "rgba(75, 192, 192, 0.2)",
                borderColor: "rgba(75, 192, 192, 1)",
                borderWidth: 1,
                data: weeklyWter.data.map((w) => w.consumo),
            },
        ],
    };

    var options = {
        scales: {
            y: {
                beginAtZero: true,
            },
        },
    };

    new Chart(ctx, {
        type: "bar",
        data: data,
        options: options,
    });
}

async function drawWeeklyElectricity(ctx) {
    const weeklyWter = await axios
        .get("/getWeeklyElectricity")
        .catch(function (error) {
            console.error("Error fetching data:", error);
            return;
        });
    var data = {
        labels: [
            "Hace 3 semanas",
            "Hace 2 semanas",
            "Semana pasada",
            "Ultimos 7 dias",
        ],
        datasets: [
            {
                label: "Consumo de agua",
                backgroundColor: "rgba(75, 192, 192, 0.2)",
                borderColor: "rgba(75, 192, 192, 1)",
                borderWidth: 1,
                data: weeklyWter.data.map((w) => w.consumo),
            },
        ],
    };

    var options = {
        scales: {
            y: {
                beginAtZero: true,
            },
        },
    };

    new Chart(ctx, {
        type: "bar",
        data: data,
        options: options,
    });
}

function clearCanvas(chartId) {
    var chart = Chart.getChart(chartId);
    if (chart) {
        chart.destroy();
    }
}
