<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IFileResource\Pages;
use App\Filament\Resources\IFileResource\RelationManagers;
use App\Models\IFile;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
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
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->icon(fn (Model $f) => $f->getIcon())
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
