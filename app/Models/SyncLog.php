<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SyncLog extends Model
{
    protected $fillable = [
        'context',
        'tax_year',
        'total_services',
        'current_service_index',
        'current_service',
        'service_progress',
        'total_chunks',
        'current_chunk',
        'chunk_progress',
        'state_classes',
        'last_key',
        'message',
        'status',
        'error',
    ];

    protected $casts = [
        'tax_year' => 'integer',
        'total_services' => 'integer',
        'current_service_index' => 'integer',
        'service_progress' => 'integer',
        'total_chunks' => 'integer',
        'current_chunk' => 'integer',
        'chunk_progress' => 'integer',
        'state_classes' => 'array',
    ];
}
