<?php

namespace App\CentralLogic;

class Helpers
{
    public static function format_coordiantes($coordinates, $return_json = false)
    {
        $data = [];
        try {

            foreach ($coordinates[0] as $coord) {
                $data[] = (object)['lat' => $coord->getlat(), 'lng' => $coord->getlng()];
            }

        } catch (\Throwable $th) {
            $data = [];
        }
        if ($return_json) {
            return response()->json($data, 200);
        }
        return $data;

    }
}
