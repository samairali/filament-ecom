<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3) // total 3 columns
                    ->schema([
                        Group::make()->schema([
                            Section::make('Product Information')->schema([
                                Grid::make()->schema([
                                    TextInput::make('name')
                                        ->required()
                                        ->unique()
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(function($operation,$state, $set) {
                                           $operation === 'create' ? $set('slug', Str::slug($state)) : null;
                                        })
                                        ->maxLength(255),
                                    TextInput::make('slug')
                                        ->required()
                                        ->unique()
                                        ->disabled()
                                        ->dehydrated(true)
                                        ->maxLength(255),
                                    MarkdownEditor::make('description')
                                        ->columnSpanFull()
                                        ->fileAttachmentsDirectory('products'),
                                ])->columns(2),
                            ]),
                            Section::make('Images')->schema([
                                FileUpload::make('images')
                                    ->multiple()
                                    ->directory('products')
                                    ->maxSize(1024)
                                    ->maxFiles(5)
                                    ->reorderable()
                            ]),
                        ])->columnSpan(2), // 2/3 width

                        Group::make()->schema([
                            Section::make('Pricing')->schema([
                                TextInput::make('price')
                                    ->required()
                                    ->numeric()
                                    ->prefix('$'),
                            ]),
                            Section::make('Associations')->schema([
                                Select::make('category_id')
                                    ->label('Category')
                                    ->relationship('category', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload(),
                                Select::make('brand_id')
                                    ->label('Brand')
                                    ->relationship('brand', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload(),
                            ])
                        ])->columnSpan(1), // 1/3 width
                    ])->columnSpanFull(),
                
                Toggle::make('is_active')
                    ->required(),
                Toggle::make('is_featured')
                    ->required(),
                Toggle::make('in_stock')
                    ->required(),
                Toggle::make('on_sale')
                    ->required(),
                
            ]);
    }
}
