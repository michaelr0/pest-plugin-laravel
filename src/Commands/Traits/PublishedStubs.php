<?php

declare(strict_types=1);

namespace Pest\Laravel\Commands\Traits;

use Illuminate\Support\Str;

trait PublishedStubs
{
    protected function resolveStubPath($stub): string
    {
        $pestStub = implode(DIRECTORY_SEPARATOR, [
            $this->laravel->basePath('vendor'),
            'pestphp',
            'pest',
            'stubs',
            $stub,
        ]);

        $customStubName = (string) Str::of($stub)
            ->replace('.php', '.stub')
            ->lower()
            ->prepend('pest.');

        $customStub = implode(DIRECTORY_SEPARATOR, [
            $this->laravel->basePath('stubs'),
            $customStubName,
        ]);

        return file_exists($customStub) ? $customStub : $pestStub;
    }
}
