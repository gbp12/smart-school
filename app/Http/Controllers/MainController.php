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

    public function getMonthlyElectricity()
    {
        $resultadosCurrentYear = $this->fetchMonthyleUse(1);
        $actualUse = $this->calculateActualUse($resultadosCurrentYear);

        return response()->json($actualUse, 200);
    }


    public function getMonthlyWater()
    {
        $resultadosCurrentYear = $this->fetchMonthyleUse(2);
        $actualUse = $this->calculateActualUse($resultadosCurrentYear);

        return response()->json($actualUse, 200);
    }

    public function getWeeklyElectricity()
    {
        $resultadosCurrentMonth = $this->fetchWeeklyUse(1);
        $actualUse = $this->calculateActualUse($resultadosCurrentMonth);
        return response()->json($actualUse, 200);
    }


    public function fetchMonthyleUse($id_type)
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
        $queryCurrentMonth = "SELECT m.id_sensor, m.consumo, m.fecha AS fecha
        FROM measurements m
        INNER JOIN (
            SELECT MAX(fecha) AS ultima_fecha
            FROM measurements
            WHERE id_sensor = $id_type
            GROUP BY  YEAR(fecha), MONTH(fecha)
        ) AS ultimas_fechas ON m.fecha = ultimas_fechas.ultima_fecha
        WHERE m.id_sensor = $id_type 
        AND YEAR(m.fecha) = YEAR(CURDATE()) 
        AND MONTH(m.fecha) = MONTH(CURDATE())
        ORDER BY m.fecha DESC;";

        $resultadosCurrentMonth = DB::select($queryCurrentMonth);
        dd($resultadosCurrentMonth);
        return $resultadosCurrentMonth;
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
}
