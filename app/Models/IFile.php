<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Model;

class IFile extends Model
{
    use HasFactory;

    protected $table = 'ifiles';

    protected $fillable = [
        'name',
        'mime_type',
        'created_by',
        'company_id'
    ];

    /**
     * Get the parent imageable model (user or post).
     */
    public function fileable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the creator wich of ifile.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the company of ifile.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function getIcon()
    {
        return match ($this->mime_type) {
            "application/vnd.garchiv.folder" => 'heroicon-m-folder',
            'application/pdf' => 'heroicon-m-document',
            default => 'heroicon-m-question-mark-circle',
        };
    }

    protected static function booted(): void
    {
        static::creating(function (IFile $iFile) {
            $iFile->company_id = auth()->user()->company_id;
        });
    }
}
