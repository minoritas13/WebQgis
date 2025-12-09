<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;

class MapController extends Controller
{
    public function show()
    {
        // Ambil file GeoJSON dari storage
        $path = 'geojson/peta.json';

        if (!Storage::disk('public')->exists($path)) {
            return response()->json(['error' => 'GeoJSON tidak ditemukan'], 404);
        }

        $geojson = Storage::disk('public')->get($path);
        $data = json_decode($geojson, true);

        return response()->json($data);
    }

    public function view()
    {

        return view('map.index'); // memanggil peta.blade.php
    }
}
