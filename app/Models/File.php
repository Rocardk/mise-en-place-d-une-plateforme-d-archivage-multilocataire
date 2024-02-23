<?php

namespace App\Models;

use App\Traits\Fileable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory, Fileable;

    protected $fillable = [
        'size',
        'url',
        'folder',
    ];
}
