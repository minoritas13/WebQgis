<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;

class MapController extends Controller
{
    public function show()
    {
        $path = 'geojson/peta.json';
        if (! Storage::disk('public')->exists($path)) {
            abort(404, 'GeoJSON tidak ditemukan');
        }

        $geojson = json_decode(Storage::disk('public')->get($path), true);

        return response()->json([
            'geojson' => $geojson,
        ]);
    }

    public function view()
    {
        return view('map.index'); // memanggil peta.blade.php
    }
}
