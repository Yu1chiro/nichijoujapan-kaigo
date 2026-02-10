<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeckResource\Pages;
use App\Models\Deck;
use App\Services\ImageKitService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DeckResource extends Resource
{
    protected static ?string $model = Deck::class;
    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationGroup = 'CBT Management';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Informasi Utama Deck')
                ->schema([
                    Forms\Components\TextInput::make('title')->required(),
                    
                    Forms\Components\Select::make('category')
                        ->options([
                            'Kaigo' => 'Kaigo',
                            'Pengolahan Makanan' => 'Pengolahan Makanan',
                            'Genba/kontruksi' => 'Genba/kontruksi',
                            'Restoran' => 'Restoran',
                            'Manufaktur' => 'Manufaktur',
                        ])
                        ->required()
                        ->native(false),
                        
                    Forms\Components\TextInput::make('access_key')
                        ->label('Access Key Ujian')
                        ->password()
                        ->revealable()
                        ->required()
                        ->dehydrated(fn($state) => filled($state)),

                    Forms\Components\TextInput::make('timer_per_question')
                        ->label('Timer (Detik)')
                        ->numeric()
                        ->default(60)
                        ->suffix('Detik'),

                    // === BAGIAN UPLOAD ===
                    Forms\Components\TextInput::make('thumbnail_url')
                        ->view('components.base64-uploader') // Blade component yang sudah Anda punya
                        ->label('Thumbnail Deck')
                        ->dehydrateStateUsing(fn ($state) => 
                            // Panggil service baru kita
                            ImageKitService::uploadBase64($state, 'deck_' . time() . '.jpg', '/decks')
                        ),
                    // =====================

                    Forms\Components\Textarea::make('description')
                        ->rows(3)
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\ImageColumn::make('thumbnail_url')->circular(),
            Tables\Columns\TextColumn::make('title')->searchable(),
            Tables\Columns\TextColumn::make('questions_count')->counts('questions')->label('Soal'),
        ])->actions([Tables\Actions\EditAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDecks::route('/'),
            'create' => Pages\CreateDeck::route('/create'),
            'edit' => Pages\EditDeck::route('/{record}/edit'),
        ];
    }
}