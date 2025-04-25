<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DownloadedContent extends Model
{
    protected $table = 'downloaded_content';
    protected $fillable = [
        'user_id',
        'original_filename',
        'converted_file',
        'downloaded_at'
    ];
    
    public $timestamps = false;
} 