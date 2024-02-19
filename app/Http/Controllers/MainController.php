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
        $curDate = "2020-07-07 08:00:00"; //Must change to current date when sensors are connected to db
        $interval = 8; //Measured in hours
        $easyQuery = "SELECT * FROM buildings";
        $query = " SELECT m.id_measure, m.consumo, m.fecha 
    FROM 
        measurements m
    WHERE 
        fecha BETWEEN DATE_SUB('$curDate', INTERVAL $interval HOUR) AND '$curDate'  
    AND 
        id_sensor = 1";
        //closest date must go second 

        //fecha >= DATE_SUB('2020-07-07 15:00:00', INTERVAL 8 HOUR)
        //WHERE event_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 1 DAY)
        $resultadosPreviousYear = DB::select($query);
        $resultadosPreviousYear2 = DB::select($easyQuery);

        dd($resultadosPreviousYear);
    }
}
