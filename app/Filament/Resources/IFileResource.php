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
        return $table
            ->modifyQueryUsing(
                fn(Builder $query) => $query->whereHasMorph(
                    'fileable',
                    [Folder::class, File::class],
                    function (Builder $query, $type) {
                        if ($type === Folder::class)
                            $query->whereNotNull('parent');
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
                    ->searchable(),
                Tables\Columns\TextColumn::make('fileable.url')
                    ->icon('heroicon-m-arrow-down-tray')
                    ->formatStateUsing(fn(string $state): string => 'Download')
                    ->url(function (IFile $if): string {
                        if (isset($if->fileable?->url))
                            return Storage::/* disk('s3')-> */ url('/' . $if->fileable?->url);
                        else
                            return "#";
                    })
                    ->openUrlInNewTab(),
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
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageIFiles::route('/'),
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
}
