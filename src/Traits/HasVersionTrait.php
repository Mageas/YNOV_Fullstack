<?php

namespace App\Traits;

trait HasVersionTrait
{
    protected function getVersion(): string
    {
        return defined('static::VERSION') ? static::VERSION : 'v1';
    }

    protected function getApiVersion(): array
    {
        return [
            'version' => $this->getVersion(),
        ];
    }

}
