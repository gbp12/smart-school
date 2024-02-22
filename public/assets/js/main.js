document.addEventListener("DOMContentLoaded", function () {
    const ctx = document.getElementById("chart1").getContext("2d");
    const ct2 = document.getElementById("chart2").getContext("2d");

    var currentIndex = 0;

    var array_of_functions = [renderWeeklyView, hola, renderEightHours, renderEveryDayLastThreeWeeks]; //

    function callNextFunction() {
    
        if (currentIndex < (array_of_functions.length)) {
            clearAll();
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


async function renderEveryDayLastThreeWeeks(ctx, ct2){
    const title = document.getElementById("viewTitle");
    title.innerHTML = "Consumo cada dia las ultimas 3 semanas";
    const threeWeeksData = await axios
    .get("/getEveryDayLastWeeks")
    .catch(function (error) {
        console.error("Error fetching data:", error);
    });

    console.log(threeWeeksData);
    let htmlBeforeElec = "<h1 class='test'> Consumo electrico en las ultimas 3 semanas</h1>";
    let htmlAfterElec = "<h2 class='test'>Hoy estamos a <span>"+threeWeeksData.data.daysLabels[6]+"</span></h2> \
    <h3 class='test'>Y hoy hemos consumido un total de <span>"+threeWeeksData.data.week1Electricity[6]+" kW/h</span> </h3>";
    let htmlBeforeWater = "<h1 class='test'> Consumo de agua en las ultimas 3 semanas</h1>";
    let htmlAfterWater = "<h2 class='test'>Hoy estamos a <span>"+threeWeeksData.data.daysLabels[6]+"</span></h2> \
    <h3 class='test'>Y hoy hemos consumido un total de <span>"+threeWeeksData.data.week1Water[6]+" litros</span> </h3>";


    $(htmlBeforeElec).insertBefore("#chart1");
    $(htmlAfterElec).insertAfter("#chart1");
    $(htmlBeforeWater).insertBefore("#chart2");
    $(htmlAfterWater).insertAfter("#chart2");


    const dataElectricity = {
        labels: threeWeeksData.data.daysLabels,
        datasets: [
          {
            label: "Semana pasada",
            data: threeWeeksData.data.week3Electricity,
            backgroundColor: "rgba(212, 119, 94, 0.6)",
          },
          {
            label: "Semana anterior",
            data: threeWeeksData.data.week2Electricity,
            backgroundColor: "rgba(240, 207, 101, 0.6)",
          },
          {
            label: "Esta semana",
            data: threeWeeksData.data.week1Electricity,
            backgroundColor: "rgba(235, 232, 170, 0.6)",
          },
        ],
      };

      const dataWater = {
        labels: threeWeeksData.data.daysLabels,
        datasets: [
          {
            label: "Semana pasada",
            data: threeWeeksData.data.week3Water,
            backgroundColor: "rgba(109, 80, 220, 0.6)",
          },
          {
            label: "Semana anterior",
            data: threeWeeksData.data.week2Water,
            backgroundColor: "rgba(128, 147, 241, 0.6)",
          },
          {
            label: "Esta semana",
            data: threeWeeksData.data.week1Water,
            backgroundColor: "rgba(114, 221, 247, 0.6)",
          },
        ],
      };

      const options = {
        indexAxis: "x", 
          responsive: true,
        aspectRatio: 1.4,
        scales: {
        y: {
            ticks: {
                color: '#666',
                font: {
                    size: '30vw',
                    weight: 'bold',
                }
            },
            beginAtZero: true,
        },
        x: {
            ticks: {
                color: '#666',
                font: {
                    size: '35vw',
                    weight: 'bold',
                }
            }
        }
    },
    plugins: {
        legend: {
            labels: {
               font:{
                size:'30vw'
               }
               }
         }
         }
    }


      const configElectricity = {
        type: "bar",
        data: dataElectricity,
        options: options,
      };

      const configWater = {
        type: "bar",
        data: dataWater,
        options: options
      };

      new Chart(ctx, configElectricity);
      new Chart(ct2, configWater);
}

async function renderEightHours(ctx, ct2){
    const title = document.getElementById("viewTitle");
    title.innerHTML = "Consumo en las ultimas 8 horas";
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

    let htmlBeforeElec = "<h1 class='test'> Consumo electrico en las ultimas 8 horas</h1>";
    let htmlAfterElec = "<h2 class='test'>Ultima medicion <span>"+EightHoursData.data.lastReadingElectricity+"</span> kW/h</h2> \
    <h3 class='test'>Tomada a las <span>"+EightHoursData.data.lastReadingElectricityDate+"</span></h3>";
    let htmlBeforeWater = "<h1 class='test'> Consumo de agua en las ultimas 8 horas</h1>";
    let htmlAfterWater = "<h2 class='test'>Ultima medicion <span>"+EightHoursData.data.lastReadingWater+"</span> kW/h</h2> \
    <h3 class='test'>Tomada a las <span>"+EightHoursData.data.lastReadingWaterDate+"</span></h3>";
    

    $(htmlBeforeElec).insertBefore("#chart1");
    $(htmlAfterElec).insertAfter("#chart1");
    $(htmlBeforeWater).insertBefore("#chart2");
    $(htmlAfterWater).insertAfter("#chart2");

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


    new Chart(ctx, {
        type: 'line',
        data: {

            labels: EightHoursData.data.electricityLabels,
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

    

   
    new Chart(ct2, {
        type: 'line',
        data: {

            labels: EightHoursData.data.waterLabels, 
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
