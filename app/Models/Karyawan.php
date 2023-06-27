<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Karyawan extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $table = "tbl_karyawan";
    protected $primaryKey = "nik";
    protected $fillable = [
        'nik',
        'nama_lengkap',
        'jabatan',
        'no_hp',
        'password',
    ];
}
