<?php

namespace App\Http\Controllers;

use App\Models\Measurement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MainController extends Controller
{
    public function index()
    {
        return view("main");
    }

    public function getMonthlyElectricity()
    {
        $resultadosCurrentYear = $this->fetchMonthlyUse(1);
        $actualUse = $this->calculateActualUse($resultadosCurrentYear);

        return response()->json($actualUse, 200);
    }


    public function getMonthlyWater()
    {
        $resultadosCurrentYear = $this->fetchMonthlyUse(2);
        $actualUse = $this->calculateActualUse($resultadosCurrentYear);

        return response()->json($actualUse, 200);
    }

    public function getWeeklyElectricity()
    {
        $resultadosCurrentMonth = $this->fetchWeeklyUse(1);
        $actualUse = $this->calculateActualUse($resultadosCurrentMonth);
        return response()->json($actualUse, 200);
    }

    public function getWeeklyWater()
    {
        $resultadosCurrentMonth = $this->fetchWeeklyUse(2);
        $actualUse = $this->calculateActualUse($resultadosCurrentMonth);
        return response()->json($actualUse, 200);
    }


    public function fetchMonthlyUse($id_type)
    {
        $queryCurrentYear = "SELECT m.id_sensor, m.consumo, m.fecha AS fecha
        FROM measurements m
        INNER JOIN (
            SELECT MAX(fecha) AS ultima_fecha
            FROM measurements
            WHERE id_sensor = $id_type
            GROUP BY YEAR(fecha), MONTH(fecha)
        ) AS ultimas_fechas ON m.fecha = ultimas_fechas.ultima_fecha
        WHERE m.id_sensor = $id_type AND YEAR(m.fecha) = YEAR(CURDATE())
        ORDER BY m.fecha DESC;";


        $queryPreviousYear = "SELECT
    m.id_sensor,
    m.consumo,
    CONCAT(YEAR(m.fecha), '-', LPAD(MONTH(m.fecha), 2, '0')) AS fecha
FROM
    measurements m
WHERE
    m.id_sensor = $id_type
    AND MONTH(m.fecha) = 12
    AND YEAR(m.fecha) = YEAR(CURDATE()) - 1
ORDER BY
    m.fecha DESC
LIMIT 1;";

        $resultadosCurrentYear = DB::select($queryCurrentYear);
        $resultadosPreviousYear = DB::select($queryPreviousYear);

        if (!empty($resultadosPreviousYear)) {
            array_push($resultadosCurrentYear, $resultadosPreviousYear[0]);
        }

        $resultadosCurrentYear = DB::select($queryCurrentYear);
        $resultadosPreviousYear = DB::select($queryPreviousYear);
        if (!empty($resultadosPreviousYear)) {
            array_push($resultadosCurrentYear, $resultadosPreviousYear[0]);
        }
        return $resultadosCurrentYear;
    }


    public function fetchWeeklyUse($id_type)
    {
        $weeklyUse = [];

        $weeklyUse[] = $this->weeklyQuery(7,  $id_type);
        $weeklyUse[] = $this->weeklyQuery(14,  $id_type);
        $weeklyUse[] = $this->weeklyQuery(21,  $id_type);
        $weeklyUse[] = $this->weeklyQuery(28,  $id_type);
        $weeklyUse[] = $this->weeklyQuery(35,  $id_type);

        return $weeklyUse;
    }

    public function weeklyQuery($start_date, $id_type)
    {
        $weekQuery = "SELECT m.id_sensor, MAX(m.consumo) AS consumo, DATE(m.fecha) as fecha
                     FROM measurements m
                     WHERE m.id_sensor = $id_type 
                     AND m.fecha BETWEEN DATE_SUB(CURDATE(), INTERVAL $start_date DAY) AND CURDATE() - INTERVAL ($start_date -7)  DAY
                     GROUP BY m.id_sensor, m.fecha
                     ORDER BY consumo DESC
                     LIMIT 1;";
        $result = DB::select($weekQuery);

        if (empty($result)) {
            $valorNulo = new Measurement();
            $valorNulo->id_sensor = $id_type;
            $valorNulo->consumo = 0;
            $valorNulo->fecha = date('Y-m-d', strtotime("-$start_date days"));
            return $valorNulo;
        }

        return $result[0];
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
                    'consumo' => $diferencia,
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
