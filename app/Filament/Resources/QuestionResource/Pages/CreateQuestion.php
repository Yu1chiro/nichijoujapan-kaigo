<?php

namespace App\Filament\Resources\QuestionResource\Pages;

use App\Filament\Resources\QuestionResource;
use App\Models\Question;
use App\Services\ImageKitService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateQuestion extends CreateRecord
{
    protected static string $resource = QuestionResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $deckId = $data['deck_id'];
        $questionsData = $data['questions_batch'] ?? [];
        $firstCreatedQuestion = null;

        foreach ($questionsData as $index => $qData) {
            
            // 1. Upload Gambar (Jika ada)
            $finalImageUrl = null;
            if (!empty($qData['thumbnail_url'])) {
                $finalImageUrl = ImageKitService::uploadBase64(
                    $qData['thumbnail_url'], 
                    'q_img_' . uniqid() . '.jpg',
                    '/questions'
                );
            }

            // 2. Audio URL (Langsung string)
            $finalAudioUrl = $qData['audio_source_url'] ?? null;

            // 3. Simpan ke Database
            $question = Question::create([
                'deck_id' => $deckId,
                'question_text' => $qData['question_text'],
                'thumbnail_url' => $finalImageUrl, // Link dari ImageKit masuk sini
                'audio_url' => $finalAudioUrl,
                'options' => $qData['options'],
                'correct_answer' => $qData['correct_answer'],
                'feedback_text' => $qData['feedback_text'],
            ]);

            if ($index === 0) {
                $firstCreatedQuestion = $question;
            }
        }

        return $firstCreatedQuestion ?? new Question();
    }

    protected function getRedirectUrl(): string 
    { 
        return $this->getResource()::getUrl('index'); 
    }
}