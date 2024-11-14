<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Model;
use Wallo\FilamentCompanies\FilamentCompanies;
use Illuminate\Support\Facades\Storage;
use App\Services\AskYourPDFService;
use Illuminate\Database\Eloquent\Builder;

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

    protected static function booted()
    {
        static::addGlobalScope('company', function (Builder $builder) {
            $builder->where('company_id', auth()->user()->currentCompany()->first()->id);
        });

        static::created(function (IFile $ifile) {

            // dd($ifile);
            if (!empty ($ifile?->fileable?->url)) {
                $mimeType = Storage::disk('public')->mimeType($ifile?->fileable?->url);

                if (
                    $ifile->fileable_type == 'App\Models\File' &&
                    AskYourPDFService::isCompatible($mimeType)
                ) {

                    // Get the content of the file
                    $fileContent = Storage::disk('public')->get($ifile->fileable->url);
                    // Get the name of the file
                    $fileName = basename($ifile->fileable->url);
                    $askYourPDFService = new AskYourPDFService();
                    $result = $askYourPDFService->uploadPDF($fileContent, $fileName);
                    // dd($result, $fileName, $fileContent);
                    $ifile->fileable->askyourpdf_id = $result->json()['docId'];
                    $ifile->fileable->save();
                    // dd($result);
                }
            }

        });

        static::deleting(function (IFile $ifile) {
            $ifile->fileable->delete();
        });
    }

    protected static function boot()
    {
        parent::boot();

        // Automatically add company_id on creation
        static::creating(function ($model) {
            $model->company_id = auth()->user()->currentCompany()->first()->id;
        });
    }
}
