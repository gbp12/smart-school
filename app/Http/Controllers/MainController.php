<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MainController extends Controller
{
    public function index()
    {
        return view("main");
    }

    public function getYearlyElectricity()
    {
        $query = "SELECT m.id_sensor, m.consumo, YEAR(m.fecha) AS fecha
    FROM measurements m
    INNER JOIN (
        SELECT MAX(fecha) AS ultima_fecha
        FROM measurements
        WHERE id_sensor = 1
        GROUP BY YEAR(fecha)
    ) AS ultimas_fechas ON m.fecha = ultimas_fechas.ultima_fecha
    WHERE m.id_sensor = 1
    ORDER BY m.fecha DESC;";

        $resultados = DB::select($query);
        return response()->json($resultados, 200);
    }

    public function getMonthlyElectricity()
    {
        // Query for the current year
        $queryCurrentYear = "SELECT m.id_sensor, m.consumo, CONCAT(YEAR(m.fecha), '-', LPAD(MONTH(m.fecha), 2, '0')) AS fecha
        FROM measurements m
        INNER JOIN (
            SELECT MAX(fecha) AS ultima_fecha
            FROM measurements
            WHERE id_sensor = 1
            GROUP BY YEAR(fecha), MONTH(fecha)
        ) AS ultimas_fechas ON m.fecha = ultimas_fechas.ultima_fecha
        WHERE m.id_sensor = 1 AND YEAR(m.fecha) = YEAR(CURDATE())
        ORDER BY m.fecha DESC;";


        $queryPreviousYear = "SELECT
    m.id_sensor,
    m.consumo,
    CONCAT(YEAR(m.fecha), '-', LPAD(MONTH(m.fecha), 2, '0')) AS fecha
FROM
    measurements m
WHERE
    m.id_sensor = 1
    AND MONTH(m.fecha) = 12
    AND YEAR(m.fecha) = YEAR(CURDATE()) - 1
ORDER BY
    m.fecha DESC
LIMIT 1;";

        // Execute queries
        $resultadosCurrentYear = DB::select($queryCurrentYear);
        $resultadosPreviousYear = DB::select($queryPreviousYear);
        //dd($resultadosPreviousYear);
        if (!empty($resultadosPreviousYear)) {

            array_push($resultadosCurrentYear, $resultadosPreviousYear[0]);
        }

        $diferencias = $this->calculateActualUse($resultadosCurrentYear);

        return response()->json($resultadosCurrentYear, 200);
    }


    public function calculateActualUse($mediciones)
    {
        $diferencias = [];

        usort($mediciones, function ($a, $b) {
            return strtotime($a->fecha) - strtotime($b->fecha);
        });

        $anteriorConsumo = null;
        foreach ($mediciones as $medicion) {
            $consumoActual = $medicion->consumo;

            if ($anteriorConsumo !== null) {
                $diferencia = $consumoActual - $anteriorConsumo;
                $diferencias[] = [
                    'fecha' => $medicion->fecha,
                    'diferencia_consumo' => $diferencia,
                ];
            }

            $anteriorConsumo = $consumoActual;
        }

        return $diferencias;
    }


    public function getLastEightHours()
    {
        $curDate = "2020-02-01 22:00:00"; //Must change to current date when sensors are connected to db
        $interval = 10; //Measured in hours

        $queryElectricity = " SELECT m.consumo, CONCAT(LPAD(HOUR(m.fecha), 2, '0'), ':', LPAD(MINUTE(m.fecha), 2, '0')) AS fecha
    FROM 
        measurements m
    WHERE 
        fecha BETWEEN DATE_SUB('$curDate', INTERVAL $interval HOUR) AND '$curDate'  
    AND 
        id_sensor = 1";
        //closest date must come second 

        $queryWater = " SELECT  m.consumo, CONCAT(LPAD(HOUR(m.fecha), 2, '0'), ':', LPAD(MINUTE(m.fecha), 2, '0')) AS fecha
    FROM 
        measurements m
    WHERE 
        fecha BETWEEN DATE_SUB('$curDate', INTERVAL $interval HOUR) AND '$curDate'  
    AND 
        id_sensor = 2";
        //sensor changed

        $electricityResults  = DB::select($queryElectricity);
        $electricityResults = $this->calculateActualUse($electricityResults);
        //dd($electricityResults);
        $waterResults = DB::select($queryWater);
        $waterResults = $this->calculateActualUse($waterResults);

        //dd($this->calculateActualUse($electricityResults));


        $totalElectricityConsumo = array_column($electricityResults, 'diferencia_consumo'); //we only need consumo
        $electricityLabels =  array_column($electricityResults, 'fecha');
        $totalWaterConsumo = array_column($waterResults, 'diferencia_consumo');
        $waterLabels =  array_column($waterResults, 'fecha');
        //dd($totalElectricityConsumo, $electricityLabels);



        $electricityAverage = $this->averageCalculator($totalElectricityConsumo);
        $waterAverage = $this->averageCalculator(($totalWaterConsumo));


        //dd($totalElectricityConsumo, $totalWaterConsumo, $waterAverage, $electricityAverage);


        $viewData = [];
        $viewData["titleWater"] = "Consumo agua 8 horas"; //Cambiar
        $viewData["titleElectricity"] = "Consumo electrico 8 horas"; //Cambiar
        $viewData["lastReadingElectricity"] = end($totalElectricityConsumo);
        $viewData["lastReadingWater"] = end($totalWaterConsumo);
        $viewData["electricityAverage"] = $electricityAverage;
        $viewData["waterAverage"] = $waterAverage;
        $viewData["lastReadingElectricityDate"] = end($electricityLabels);
        $viewData["lastReadingWaterDate"] = end($waterLabels);


        $data["totalElectricityConsumo"] = $totalElectricityConsumo;
        $data["electricityLabels"] = $electricityLabels;
        $data["totalWaterConsumo"] = $totalWaterConsumo;
        $data["waterLabels"] = $waterLabels;
        $data["electricityAverage"] = $electricityAverage;
        $data["waterAverage"] = $waterAverage;

        //$data["electricityThreshold"] = [min($totalElectricityConsumo), max($totalElectricityConsumo)]; //might not be needed | might be simplified in function
        //$data["waterThreshold"] = [min($totalWaterConsumo), max($totalWaterConsumo)]; //ditto

        return view('eightHour', compact('data'))->with("verDatos", $viewData);
    }

    public function averageCalculator($array)
    {
        if (count($array)) {
            $a = array_filter($array);
            $average = array_sum($a) / count($a);
            return round($average, 2);
        }
        return 0;
    }
}
