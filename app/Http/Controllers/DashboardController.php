<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB; // Import namespace DB
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $hariini = date('Y-m-d');
        $nik = Auth::guard('karyawan')->user()->nik;
        $presensihariini = DB::table('tbl_presensi')->where('tgl_presensi', $hariini)->first();
        return view('dashboard.dashboard', compact('presensihariini'));
    }
}
