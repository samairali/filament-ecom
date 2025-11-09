<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Grid::make()
                            ->schema([
                                TextInput::make('name')
                                    ->label('Category Name')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($operation, $state, $set) {
                                        $operation === 'create' ? $set('slug', Str::slug($state)) : null;
                                    }),
                                TextInput::make('slug')
                                    ->label('Slug')
                                    ->required()
                                    ->disabled()
                                    ->dehydrated(true)
                                    ->maxLength(255),
                            ]),
                        FileUpload::make('image')
                            ->label('Category Image')
                            ->image()
                            ->required(),
                        Toggle::make('is_active')
                            ->label('Is Active')
                            ->default(true),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
