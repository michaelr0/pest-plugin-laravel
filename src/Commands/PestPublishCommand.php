<?php

declare(strict_types=1);

namespace Pest\Laravel\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Events\PublishingStubs;
use Illuminate\Support\Str;

/**
 * @internal
 */
final class PestPublishCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pest:publish
                    {--existing : Publish and overwrite only the files that have already been published}
                    {--force : Overwrite any existing files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish Pest test stubs for customization';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $pestStubsPath = implode(DIRECTORY_SEPARATOR, [
            $this->laravel->basePath('vendor'),
            'pestphp',
            'pest',
            'stubs',
        ]);

        $stubsPath = $this->laravel->basePath('stubs');

        if (! is_dir($stubsPath)) {
            (new Filesystem)->makeDirectory($stubsPath);
        }

        $stubs = [
            'Browser.php',
            'Dataset.php',
            'Feature.php',
            'Unit.php',
        ];

        $this->laravel['events']->dispatch($event = new PublishingStubs($stubs));

        foreach ($event->stubs as $stub) {
            $pestStub = implode(DIRECTORY_SEPARATOR, [
                $pestStubsPath,
                $stub,
            ]);

            $customStubName = (string) Str::of($stub)
                ->replace('.php', '.stub')
                ->lower()
                ->prepend('pest.');

            $customStub = implode(DIRECTORY_SEPARATOR, [
                $stubsPath,
                $customStubName,
            ]);

            if ((! $this->option('existing') && (! file_exists($customStub) || $this->option('force')))
                || ($this->option('existing') && file_exists($customStub))) {
                file_put_contents($customStub, file_get_contents($pestStub));
            }
        }

        $this->components->info('Pest test stubs published successfully.');
    }
}
