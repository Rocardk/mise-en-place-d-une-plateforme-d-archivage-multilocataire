<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Model;
use Wallo\FilamentCompanies\FilamentCompanies;

class IFile extends Model
{
    use HasFactory;

    protected $table = 'ifiles';

    protected $fillable = [
        'name',
        'mime_type',
        'created_by'
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

    public function getIcon()
    {
        return match ($this->mime_type) {
            "application/vnd.garchiv.folder" => 'heroicon-m-folder',
            'application/pdf' => 'heroicon-m-document',
            default => 'heroicon-m-question-mark-circle',
        };
    }

    /**
     * Get the company that the IFile belongs to.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(FilamentCompanies::companyModel());
    }
}
