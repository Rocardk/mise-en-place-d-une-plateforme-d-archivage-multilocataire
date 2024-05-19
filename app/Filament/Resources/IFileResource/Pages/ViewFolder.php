<?php

namespace App\Filament\Resources\IFileResource\Pages;

use App\Filament\Resources\IFileResource;
use Filament\Resources\Pages\Page;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Support\Facades\Storage;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Models\IFile;
use App\Models\File;
use App\Models\Folder;
use App\Services\AskYourPDFService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Actions;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ViewFolder extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;
    use InteractsWithRecord;
    protected static string $resource = IFileResource::class;

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
    }

    protected static string $view = 'filament.resources.i-file-resource.pages.view-folder';

    public function table(Table $table): Table
    {
        $ifiles = IFile::whereHasMorph(
            'fileable',
            [Folder::class, File::class],
            function (Builder $query, $type) {
                match ($type) {
                    Folder::class => $query->whereParent($this->record->fileable->id),
                    File::class => $query->whereFolder($this->record->fileable->id),
                };
            }
        );

        return $table
            ->query(
                $ifiles
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->icon(fn(Model $f) => $f->getIcon())
                    ->searchable(),
                Tables\Columns\TextColumn::make('createdBy.name')
                    ->label('Propriétaire')
                    ->sortable(),
                Tables\Columns\TextColumn::make('mime_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('Download')
                    ->link()
                    ->icon('heroicon-m-arrow-down-tray')
                    ->url(function (IFile $if): string {
                        return Storage::disk('public')->url('/' . $if->fileable?->url);
                    })->visible(fn(IFile $if) => isset($if->fileable?->url))
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('Open')
                    ->link()
                    ->icon('heroicon-m-folder-open')
                    ->url(fn(IFile $if) => route('filament.dashboard.resources.i-files.folder', ['record' => $if->id]))
                    ->visible(fn(IFile $if) => !isset($if->fileable?->url)),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            /* ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]) */ ;
    }

    public static function getNavigationLabel(): string
    {
        return 'Détails';
    }

    public function getTitle(): string
    {
        return $this->record->name;
    }
    public function getBreadcrumb(): ?string
    {
        return 'Détails';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make("create.folder")
                ->modalHeading('Create folder')
                ->form([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                ])
                ->label('Create folder')->using(function (array $data): Model {

                    $folder = new Folder([
                        'parent' => $this->record->fileable->id,
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
                ->form([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\FileUpload::make('file')
                        ->label('Select File')
                        // ->disk('s3')
                        // ->directory('form-attachments')
                        // ->visibility('private')
                        // ->acceptedFileTypes(['*'])
                        ->openable()
                        ->downloadable()
                        ->required()->columnSpanFull(),
                ])
                ->label('Create file')->using(function (array $data) {
                    // dd($data);
                    // $f = Storage::disk('s3')->response($data['file']);
                    $mimeType = Storage::disk('public')->mimeType($data['file']);
                    $size = Storage::disk('public')->size($data['file']);

                    $file = new File([
                        'url' => $data['file'],
                        'size' => $size,
                        'folder' => $this->record->fileable->id,
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
}
