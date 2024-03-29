<?php

namespace App\Models;

use App\Traits\Fileable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    use HasFactory, Fileable;

    protected $fillable = [
        'parent'
    ];
}
