document.addEventListener("DOMContentLoaded", function () {
    const ctx = document.getElementById("chart1").getContext("2d");
    const ct2 = document.getElementById("chart2").getContext("2d");

    var currentIndex = 0;

    var array_of_functions = [renderWeeklyView, hola];

    function callNextFunction() {
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

function hola() {
    console.log("hola");
    const title = document.getElementById("viewTitle");
    title.innerHTML = "pene";
}

async function renderWeeklyView(ctx, ct2) {
    const title = document.getElementById("viewTitle");
    title.innerHTML = "Consumo semanal";
    clearCanvas("chart1");
    drawWeeklyWater(ctx);
    clearCanvas("chart2");
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
