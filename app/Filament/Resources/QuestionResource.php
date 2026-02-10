<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuestionResource\Pages;
use App\Models\Question;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class QuestionResource extends Resource
{
    protected static ?string $model = Question::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'CBT Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detail Paket Soal')
                    ->schema([
                        Forms\Components\Select::make('deck_id')
                            ->relationship('deck', 'title')
                            ->required()
                            ->searchable()
                            ->preload(),
                    ]),

                // REPEATER (CREATE MODE)
                Forms\Components\Repeater::make('questions_batch')
                    ->label('Input Soal Banyak (Batch)')
                    ->schema(self::getQuestionFormSchema(true))
                    ->visible(fn ($livewire) => $livewire instanceof Pages\CreateQuestion)
                    ->columnSpanFull(),

                // SINGLE FORM (EDIT MODE)
                Forms\Components\Group::make(self::getQuestionFormSchema(false))
                    ->visible(fn ($livewire) => $livewire instanceof Pages\EditQuestion)
                    ->columnSpanFull(),
            ]);
    }

    // Schema Form dipisah agar rapi
    protected static function getQuestionFormSchema(bool $isCreateMode): array
    {
        return [
            Forms\Components\RichEditor::make('question_text')
                ->label('Teks Soal')
                ->required()
                ->columnSpanFull(),
            
            Forms\Components\Grid::make(2)->schema([
                // GAMBAR (Pake Component Base64 Uploader Anda)
                Forms\Components\TextInput::make('thumbnail_url')
                    ->label('Gambar Soal (Opsional)')
                    ->view('components.base64-uploader'), 

                // AUDIO (Input URL Manual - Vercel Safe)
                Forms\Components\TextInput::make($isCreateMode ? 'audio_source_url' : 'new_audio_source_url')
                    ->label('URL Audio MP3 (Link Luar)')
                    ->placeholder('https://contoh.com/audio.mp3'),
                
                // Field Readonly untuk melihat URL tersimpan (hanya di edit)
                Forms\Components\TextInput::make('audio_url')
                    ->label('URL Audio Tersimpan')
                    ->visible(!$isCreateMode)
                    ->disabled(),
            ]),

            Forms\Components\Repeater::make('options')
                ->label('Pilihan Jawaban')
                ->schema([Forms\Components\TextInput::make('text')->required()])
                ->defaultItems(4)
                ->grid(2)
                ->columnSpanFull(),

            Forms\Components\Select::make('correct_answer')
                ->label('Kunci Jawaban')
                ->options(['0' => 'A', '1' => 'B', '2' => 'C', '3' => 'D'])
                ->required(),
                
            Forms\Components\RichEditor::make('feedback_text')
                ->label('Pembahasan')
                ->columnSpanFull(),
        ];
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\ImageColumn::make('thumbnail_url')->circular()->label('Img'),
            Tables\Columns\IconColumn::make('audio_url')->icon('heroicon-o-speaker-wave')->boolean()->label('Audio'),
            Tables\Columns\TextColumn::make('deck.title')->badge(),
            Tables\Columns\TextColumn::make('question_text')->html()->limit(40),
        ])->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuestions::route('/'),
            'create' => Pages\CreateQuestion::route('/create'),
            'edit' => Pages\EditQuestion::route('/{record}/edit'),
        ];
    }
}