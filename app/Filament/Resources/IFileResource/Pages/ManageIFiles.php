<?php

namespace App\Filament\Resources\IFileResource\Pages;

use App\Filament\Resources\IFileResource;
use App\Models\File;
use App\Models\Folder;
use App\Models\IFile;
use Filament\Actions;
use Illuminate\Database\Eloquent\Builder;
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

                    $root = $this->getRootFolderOrCreate();

                    $folder = new Folder([
                        'parent' => $root->id,
                    ]);

                    $ifile = new IFile([
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
                ->label('Create file')->using(function (array $data, string $model) {
                    // dd($data);
                    // $f = Storage::disk('s3')->response($data['file']);
                    $mimeType = Storage::disk('public')->mimeType($data['file']);
                    $size = Storage::disk('public')->size($data['file']);

                    $root = $this->getRootFolderOrCreate();

                    $file = new File([
                        'url' => $data['file'],
                        'size' => $size,
                        'folder' => $root->id,
                    ]);

                    // dd($size, $mimeType/* , $file */);
        
                    $ifile = new IFile([
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

    protected function getRootFolderOrCreate(): Folder
    {
        $root_ifile = IFile::whereHasMorph('fileable', [Folder::class], function ($query, $type) {
            if ($type === Folder::class) {
                $query->where('parent', null);
            }
        })->whereCreatedBy(auth()->id())->first();

        // $parent = Folder::Where('id', $root_ifile->fileable->getKey())->first();

        // dd($parent);

        if (empty($root_ifile)) {
            $parent = new Folder();
            $root_ifile = new IFile([
                'name' => auth()->id() . '__ROOT__',
                'created_by' => auth()->id(),
                'mime_type' => 'application/vnd.garchiv.folder',
            ]);

            $parent->save();

            $parent->file()->save($root_ifile);
        }

        return $root_ifile->fileable;
    }
}
