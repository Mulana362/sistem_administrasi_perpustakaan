<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Member; // atau model anggota kamu

class AnggotaLookupController extends Controller
{
    public function byNis($nis)
    {
        $anggota = Member::where('nis', $nis)->first(); // sesuaikan nama model/tabel

        if (!$anggota) {
            return response()->json(['found' => false]);
        }

        return response()->json([
            'found' => true,
            'data' => [
                'nama' => $anggota->name ?? $anggota->nama,
                'kelas' => $anggota->class ?? $anggota->kelas,
            ]
        ]);
    }
}
