<?php

namespace Thiktak\FilamentPlugins\Filament\Pages;

use Filament\Pages\Page;
use Symfony\Component\Process\Process;

class AboutPluginsList extends Page
{
    protected static string $view = 'thiktak-filament-plugins::filament.pages.about-plugins-list';

    public function getViewData(): array
    {

        dd(
            ShellCommand::execute('cd .. && composer') //composer show -l')
        );

        return [
            'packages' => [1],
        ];
    }
}

class ShellCommand
{
    public static function execute($cmd): string
    {
        $process = Process::fromShellCommandline($cmd);

        putenv('COMPOSER_HOME=' . app_path('../vendor/bin/composer'));

        $processOutput = '';

        $captureOutput = function ($type, $line) use (&$processOutput) {
            $processOutput .= $line;
        };

        $process->setTimeout(null)
            ->run($captureOutput);

        if ($process->getExitCode()) {
            $exception = new \Exception($cmd . ' - ' . $processOutput);
            report($exception);

            throw $exception;
        }

        return $processOutput;
    }
}
