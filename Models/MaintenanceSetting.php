<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceSetting extends Model
{
    use HasFactory;
    protected $fillable = ['page_name', 'is_open'];
}
