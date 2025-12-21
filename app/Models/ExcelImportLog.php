<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExcelImportLog extends Model
{
    protected $fillable = [
        'type',
        'file_name',
        'file_path',
        'created_count',
        'created_ids',
        'imported_at',
    ];

    protected $casts = [
        'imported_at' => 'datetime',
    ];
}
