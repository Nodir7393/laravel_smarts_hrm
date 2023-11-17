<?php

namespace Modules\Envato\Services\ReplacerBot;

final class ScriptManagerResponseDTO 
{
    protected array $message;
    public string $errorMessage;
    public bool $isResponse;
    public bool $isEnd;
    public array $data;
    public bool $isError;

    public function __construct(array $message = [], bool $isResponse = false, bool $isEnd = false, array $data = [], string $errorMessage = '', bool $isError = false)
    {
        $this->message = $message;
        $this->errorMessage = $errorMessage;
        $this->isError = $isError;
        $this->isResponse = $isResponse;
        $this->isEnd = $isEnd;
        $this->data = $data;
    }

    public function getMessage() :string
    {
        return join('\n', $this->message);
    }
}