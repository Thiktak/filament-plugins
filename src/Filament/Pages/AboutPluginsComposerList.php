<?php

namespace Thiktak\FilamentPlugins\Filament\Pages;

use Composer\Console\Application;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use Thiktak\FilamentPlugins\Helpers\ComparePackages;

class AboutPluginsComposerList extends Page
{
    protected static string $view = 'thiktak-filament-plugins::filament.pages.about-plugins-composer-list';

    protected static ?string $navigationGroup = 'Settings';

    public ?string $search = null;

    protected $queryString = ['search'];

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('filament')
                ->label('Explore Filament plugins')
                ->color('warning')
                ->url('https://filamentphp.com/plugins')
                ->openUrlInNewTab(),

            Action::make('run')
                ->label('Check composer')
                ->color('info')
                ->action(function () {
                    Cache::forget('list-of-packages');
                    Cache::flush();
                    $this->getViewData();
                    $this->dispatch('$refresh');
                }),
        ];
    }

    public function getViewData(): array
    {
        //Cache::forget('list-of-packages');
        $packages = ComparePackages::updateCaches();
        //$this->search = 'awcodes/filament-table-repeater';

        return [
            'lastModified' => new \Carbon\Carbon($packages['lastModified']),
            'total' => count($packages['data']),
            'packages' => collect($packages['data'] ?? [])
                ->when(! empty($this->search), function ($query) {
                    $words = array_filter(array_map('trim', explode(' ', $this->search)));
                    foreach ($words as $word) {
                        $query = $query
                            ->filter(function ($data) use ($word) {
                                return \Illuminate\Support\Str::contains($data['dist']['name'], $word)
                                    || \Illuminate\Support\Str::contains($data['dist']['description'], $word)
                                    || collect($data['dist']['keywords'])->filter(
                                        fn ($keyword) => \Illuminate\Support\Str::contains($keyword, $word)
                                    )->count() > 0;
                            });
                    }

                    return $query;
                }),
        ];
    }

    public function getImage(array $package)
    {
        //dd($packageData['dist']);

        $url = str_replace('.git', '', $package['data']->getSourceUrl());

        return Cache::remember(md5($url), 15 * 60, function () use ($url) {
            return collect(get_meta_tags($url))
                ->only('twitter:image:src', 'og:image')
                ->first();
        });
    }

    /*dd(
            ShellCommand::execute('cd .. && composer') //composer show -l')
        );*/
    /*echo app_path('../vendor/bin/composer');
        putenv('COMPOSER_HOME=' . app_path('../vendor/bin/composer'));


        $stream = fopen('php://temp', 'w+');
        $output = new StreamOutput($stream);
        $application = new Application();
        $application->setAutoExit(false);
        $code = $application->run(new ArrayInput(array('command' => '-v')), $output);
        dd(stream_get_contents($stream));

        $app = new \Composer\Console\Application();

        $factory = new \Composer\Factory();
        $output = $factory->createOutput();

        $input = new \Symfony\Component\Console\Input\ArrayInput(array(
            'command' => 'help',
        ));

        $stream = fopen('php://temp', 'w+');
        $output = new StreamOutput($stream);

        $input->setInteractive(false);
        //$app->setAutoExit(false);

        echo "<pre>";
        $cmdret = $app->doRun($input, $output); //unfortunately ->run() call exit() so we use doRun()
        echo "end!";

        dd($cmdret, $output, stream_get_contents($stream)); //($output->getStream()));
    }
//*/
}
