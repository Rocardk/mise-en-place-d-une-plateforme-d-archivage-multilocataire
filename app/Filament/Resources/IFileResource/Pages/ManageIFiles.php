<?php

namespace App\Filament\Resources\IFileResource\Pages;

use App\Filament\Resources\IFileResource;
use App\Models\File;
use App\Models\Folder;
use App\Models\IFIle;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Termwind\Components\Dd;

class ManageIFiles extends ManageRecords
{
    protected static string $resource = IFileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make("create.folder")
                ->modalHeading('Create folder')
                ->label('Create folder')->using(function (array $data, string $model): Model {

                    $folder = new Folder();

                    $ifile = new IFIle([
                        ...$data,
                        'created_by' => auth()->id(),
                        'mime_type' => 'application/vnd.garchiv.folder',
                    ]);

                    $folder->save();

                    $folder->file()->save($ifile);

                    return $ifile;
                }),
            Actions\CreateAction::make("create.file")
                ->modalHeading('Create file')
                ->label('Create file')->using(function (array $data, string $model): Model {
                    // $f = Storage::disk('public')->response($data['file']);
                    $mimeType = Storage::disk('public')->mimeType($data['file']);
                    $size = Storage::disk('public')->size($data['file']);


                    $file = new File([
                        'url' => $data['file'],
                        'size' => $size,
                        'folder' => IFIle::whereCreatedBy(auth()->id())->where('fileable_type', 'App\Models\Folder')->first()->id,
                    ]);


                    // dd($size, $mimeType/* , $file */);

                    $ifile = new IFIle([
                        ...$data,
                        'created_by' => auth()->id(),
                        'mime_type' => $mimeType,
                    ]);

                    $file->save();

                    $file->file()->save($ifile);

                    return $ifile;
                }),
        ];
    }
}
