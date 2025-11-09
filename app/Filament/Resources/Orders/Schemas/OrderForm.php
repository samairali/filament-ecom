<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Models\Product;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\ToggleButtons;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Number;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()->schema([
                    Section::make('Order Information')->schema([
                        Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('payment_method')
                            ->options([
                                'credit_card' => 'Credit Card',
                                'paypal' => 'PayPal',
                                'bank_transfer' => 'Bank Transfer',
                                'cash_on_delivery' => 'Cash on Delivery',
                            ])
                            ->default('cash_on_delivery')
                            ->required(),
                        Select::make('payment_status')
                            ->options([
                                'pending' => 'Pending',
                                'completed' => 'Completed',
                                'failed' => 'Failed',
                                'refunded' => 'Refunded',
                            ])
                            ->default('pending')
                            ->required(),
                        ToggleButtons::make('status')
                            ->options([
                                'new' => 'New',
                                'processing' => 'Processing',
                                'shipped' => 'Shipped',
                                'delivered' => 'Delivered',
                                'cancelled' => 'Cancelled',
                            ])
                            ->default('new')
                            ->inline()
                            ->colors([
                                'new' => 'primary',
                                'processing' => 'warning',
                                'shipped' => 'info',
                                'delivered' => 'success',
                                'cancelled' => 'danger',
                            ])
                            ->icons([
                                'new' => 'heroicon-o-plus',
                                'processing' => 'heroicon-o-arrow-path',
                                'shipped' => 'heroicon-o-truck',
                                'delivered' => 'heroicon-o-check',
                                'cancelled' => 'heroicon-o-x-mark',
                            ])
                            ->required(),
                        Select::make('currency')
                            ->options([
                                'usd' => 'USD',
                                'eur' => 'EUR',
                                'gbp' => 'GBP',
                            ])
                            ->default('usd')
                            ->required(),
                        Select::make('shipping_method')
                            ->options([
                                'fedex' => 'FedEx',
                                'ups' => 'UPS',
                                'usps' => 'USPS',
                            ])
                            ->default('fedex')
                            ->required(),
                        Textarea::make('notes')
                            ->rows(3)
                            ->placeholder('Additional notes about the order'),
                        

                    ]),
                ])->columnSpanFull(),
                Group::make()->schema([
                    Section::make('Order Items')->schema([
                        Repeater::make('items')
                            ->relationship()
                            ->schema([
                                Grid::make(4)->schema([
                                    Select::make('product_id')
                                        ->relationship('product', 'name')
                                        ->required()
                                        ->searchable()
                                        ->preload()
                                        ->distinct()
                                        ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                        ->reactive()
                                        ->afterStateUpdated(function($state, callable $set, callable $get) {
                                            $product = Product::find($state);
                                            if ($product) {
                                                $set('unit_amount', $product->price);
                                                $set('total_amount', $product->price * ($get('quantity') ?? 1));
                                            } else {
                                                $set('unit_amount', 0);
                                                $set('total_amount', 0);
                                            }
                                        })
                                        ->columnSpan(1),

                                    TextInput::make('quantity')
                                        ->required()
                                        ->numeric()
                                        ->minValue(1)
                                        ->default(1)
                                        ->reactive()
                                        ->afterStateUpdated(function($state, callable $set, callable $get) {
                                            $unitAmount = $get('unit_amount') ?? 0;
                                            $set('total_amount', $unitAmount * $state);
                                        })
                                        ->columnSpan(1),

                                    TextInput::make('unit_amount')
                                        ->required()
                                        ->numeric()
                                        ->readOnly()
                                        ->dehydrated()
                                        ->columnSpan(1),
                                    TextInput::make('total_amount')
                                        ->required()
                                        ->numeric()
                                        ->readOnly()
                                        ->dehydrated()
                                        ->columnSpan(1),
                                ]),
                                
                            ]),
                            TextEntry::make('grand_total_display')
                                ->label('Grand Total')
                                ->state(function ($get, $set) {
                                    $total = 0;

                                    if ($repeaters = $get('items')) {
                                        foreach ($repeaters as $key => $item) {
                                            $total += $get("items.{$key}.total_amount") ?? 0;
                                        }
                                    }
                                    $set('grand_total', $total);
                                    return Number::currency($total, 'USD');
                                })
                    ])->columnSpan(12),
                ])->columnSpanFull(),
                Hidden::make('grand_total')->dehydrated()               
            ]);
    }
}
