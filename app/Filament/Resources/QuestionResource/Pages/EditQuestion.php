<?php

namespace App\Filament\Resources\QuestionResource\Pages;

use App\Filament\Resources\QuestionResource;
use App\Services\ImageKitService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditQuestion extends EditRecord
{
    protected static string $resource = QuestionResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // 1. Cek Update Gambar
        if (!empty($data['thumbnail_url'])) {
             // Fungsi ini otomatis cek: kalau stringnya URL lama -> dilewatkan.
             // Kalau stringnya Base64 baru -> diupload.
             $newImgUrl = ImageKitService::uploadBase64(
                 $data['thumbnail_url'], 
                 'q_img_upd_' . uniqid() . '.jpg',
                 '/questions'
             );
             
             if ($newImgUrl) {
                 $data['thumbnail_url'] = $newImgUrl;
             }
        }

        // 2. Cek Update Audio
        if (!empty($data['new_audio_source_url'])) {
            $data['audio_url'] = $data['new_audio_source_url'];
            unset($data['new_audio_source_url']);
        }

        return $data;
    }

    protected function getHeaderActions(): array 
    { 
        return [Actions\DeleteAction::make()]; 
    }
}