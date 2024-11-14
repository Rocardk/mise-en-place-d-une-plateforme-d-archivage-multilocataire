<?php

namespace App\Services;

use App\Models\File;
use Illuminate\Support\Facades\Http;

class AskYourPDFService
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = env('ASKYOURPDF_API_KEY');
        $this->baseUrl = env('ASKYOURPDF_BASE_URL');
    }

    public function uploadPDF($fileContent, $fileName)
    {
        $response = Http::withHeaders([
            'x-api-key' => "{$this->apiKey}",
        ])->attach('file', $fileContent, $fileName)
            ->post("{$this->baseUrl}/v1/api/upload");


        $response->throw();

        return $response/* ->json() */ ;
    }

    public function askQuestionToAllMyDocuments($question, $oldMessages = [])
    {

        $files = File::where("askyourpdf_id", "<>", NULL)->get()->map(fn($f) => $f->askyourpdf_id);

        /* dd($files, $question, [
            "documents" => $files,
            "messages" => [
                ...$oldMessages,
                [
                    "sender" => "User",
                    "message" => $question
                ]
            ]
        ]); */

        $response = Http::withHeaders([
            'x-api-key' => "{$this->apiKey}",
        ])->post("{$this->baseUrl}/v1/api/knowledge_base_chat", [
                    "documents" => $files,
                    "messages" => [
                        ...$oldMessages,
                        [
                            "sender" => "User",
                            "message" => $question
                        ]
                    ]
                ]);

        $response->throw();

        return $response/* ->json() */ ;
    }

    static function isCompatible($mimeType)
    {
        // Define an array of specific supported MIME types
        $supportedMimeTypes = [
            'application/pdf',          // .pdf
            'application/vnd.ms-powerpoint',          // .ppt
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',  // .pptx
            'text/csv',                 // .csv
            'application/epub+zip',     // .epub
            'application/rtf',          // .rtf
        ];

        // Define an array of Office MIME types
        $officeMimeTypes = [
            'application/msword',                                         // .doc
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',  // .docx
            'application/vnd.oasis.opendocument.text',                    // .odt
            'application/vnd.ms-excel',                                   // .xls
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',        // .xlsx
            'application/vnd.oasis.opendocument.spreadsheet',             // .ods
            'application/vnd.ms-powerpoint',                              // .ppt
            'application/vnd.openxmlformats-officedocument.presentationml.presentation', // .pptx
            'application/vnd.ms-powerpoint.slideshow.macroEnabled.12',    // .ppsm
            'application/vnd.ms-powerpoint.presentation.macroEnabled.12', // .pptm
            'application/vnd.ms-excel.sheet.macroEnabled.12',             // .xlsm
            'application/vnd.ms-excel.template.macroEnabled.12',          // .xltm
            'application/vnd.ms-word.document.macroEnabled.12',           // .docm
            'application/vnd.ms-word.template.macroEnabled.12',           // .dotm
        ];

        // Check if the given MIME type is in the array of specific supported MIME types
        if (in_array($mimeType, $supportedMimeTypes)) {
            return true;
        }

        // Check if the given MIME type starts with 'text/'
        if (substr($mimeType, 0, 5) === 'text/') {
            return true;
        }

        // Check if the given MIME type is in the array of Office MIME types
        if (in_array($mimeType, $officeMimeTypes)) {
            return true;
        }

        // If none of the conditions are met, return false
        return false;
    }
}
