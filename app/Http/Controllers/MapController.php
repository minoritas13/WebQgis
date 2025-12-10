<?php

namespace App\Http\Controllers;

class MapController extends Controller
{
    public function show()
    {
        $path = public_path('geojson/peta.json'); // ambil dari public/geojson
        
        if (! file_exists($path)) {
            abort(404, 'GeoJSON tidak ditemukan');
        }
        $geojson = json_decode(file_get_contents($path), true);

        return response()->json(['geojson' => $geojson]);
    }

    public function view()
    {
        return view('map.index'); // memanggil peta.blade.php
    }
}
