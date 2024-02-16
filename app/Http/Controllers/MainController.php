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
        array_push($resultadosCurrentYear, $resultadosPreviousYear[0]);

        $diferencias = $this->calculateActualUse($resultadosCurrentYear);

        return response()->json($diferencias, 200);
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
}
