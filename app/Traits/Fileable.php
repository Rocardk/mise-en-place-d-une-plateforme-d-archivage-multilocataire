<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Relations\MorphOne;
use App\Models\IFile;

trait Fileable
{
    /**
     * Get the model's file.
     */
    public function file(): MorphOne
    {
        return $this->morphOne(IFile::class, 'fileable');
    }

    public static function boot()
    {
        parent::boot();
        static::deleting(function ($model) {
            // Delete related IFile
            $model->file()->delete();
        });
    }
}
