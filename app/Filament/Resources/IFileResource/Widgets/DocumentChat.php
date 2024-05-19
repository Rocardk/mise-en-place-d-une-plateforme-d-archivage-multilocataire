<?php

namespace App\Filament\Resources\IFileResource\Widgets;

use App\Services\AskYourPDFService;
use Filament\Widgets\Widget;

class DocumentChat extends Widget
{
    protected static string $view = 'filament.resources.i-file-resource.widgets.document-chat';

    public array $messages;

    public string $question;

    public function mount()
    {
        // Initialize the messages array
        $this->messages = [];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        return [
            'messages' => $this->messages,
        ];
    }

    public function ask()
    {

        // dd($this->messages);


        $this->messages[] = [
            'sender' => 'user',
            'message' => $this->question,
        ];

        // dd($this->messages);

        $askYourPDFService = new AskYourPDFService();

        $result = $askYourPDFService->askQuestionToAllMyDocuments(
            $this->question,
            $this->messages
        );

        // $this->question = "";
        $this->reset('question');

        // dd($result, $fileName, $fileContent);
        $this->messages[] = $result->json()['answer'];
    }
}
