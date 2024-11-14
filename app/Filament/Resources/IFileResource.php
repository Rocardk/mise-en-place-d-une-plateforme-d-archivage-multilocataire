<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IFileResource\Pages;
use App\Filament\Resources\IFileResource\RelationManagers;
use App\Models\IFile;
use App\Models\File;
use App\Models\Folder;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Storage;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class IFileResource extends Resource
{
    protected static ?string $model = IFile::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\FileUpload::make('file')
                    ->label('Select File')
                    // ->disk('s3')
                    // ->directory('form-attachments')
                    // ->visibility('private')
                    // ->acceptedFileTypes(['*'])
                    ->visibleOn('create.file')
                    ->openable()
                    ->downloadable()
                    ->required()->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        $root = static::getRootFolderOrCreate();

        return $table
            ->modifyQueryUsing(
                fn(Builder $query) => $query->whereHasMorph(
                    'fileable',
                    [Folder::class, File::class],
                    fn(Builder $query, $type) => match ($type) {
                        Folder::class => $query->whereParent($root->id),
                        File::class => $query->whereFolder($root->id),
                    }
                )
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->icon(fn(Model $f) => $f->getIcon())
                    ->searchable(),
                Tables\Columns\TextColumn::make('createdBy.name')
                    ->label('Propriétaire')
                    ->sortable(),
                Tables\Columns\TextColumn::make('mime_type')
                    ->label('Type')
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
                    })->visible(fn(IFile $if) => isset ($if->fileable?->url))
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('Open')
                    ->link()
                    ->icon('heroicon-m-folder-open')
                    ->url(fn(IFile $if) => route('filament.dashboard.resources.i-files.folder', ['record' => $if->id]))
                    ->visible(fn(IFile $if) => !isset ($if->fileable?->url)),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            /* ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]) */ ;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageIFiles::route('/'),
            'folder' => Pages\ViewFolder::route('/{record}/folder'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return 'Mes Archives';
    }

    public static function getLabel(): ?string
    {
        return 'Fichier';
    }

    public static function getPluralLabel(): ?string
    {
        return 'Fichiers';
    }

    protected static function getRootFolderOrCreate(): Folder
    {
        $root_ifile = IFile::whereHasMorph('fileable', [Folder::class], function ($query, $type) {
            if ($type === Folder::class) {
                $query->where('parent', null);
            }
        })->first();//->whereCreatedBy(auth()->id());

        // $parent = Folder::Where('id', $root_ifile->fileable->getKey())->first();

        // dd($parent);

        if (empty($root_ifile)) {
            $parent = new Folder();
            $root_ifile = new IFile([
                'name' => auth()->user()->currentCompany()->first()->id . '__ROOT__',
                'created_by' => auth()->id(),
                'mime_type' => 'application/vnd.garchiv.folder',
            ]);

            $parent->save();

            $parent->file()->save($root_ifile);
        }

        return $root_ifile->fileable;
    }


    public static function getWidgets(): array
    {
        return [
            \App\Filament\Resources\IFileResource\Widgets\DocumentChat::class,
        ];
    }
}
