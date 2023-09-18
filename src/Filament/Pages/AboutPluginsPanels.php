<?php

namespace Thiktak\FilamentPlugins\Filament\Pages;

use Filament\Pages\Page;
use Symfony\Component\Process\Process;

use Composer\Console\Application;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Panel;
use Illuminate\Cache\FileStore;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\HtmlString;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use Thiktak\FilamentPlugins\Helpers\ComparePackages;
use Thiktak\FilamentPlugins\Vendors\Shadiakiki1986\ComposerWrapper;

class AboutPluginsPanels extends Page implements HasInfolists
{
    use InteractsWithInfolists;

    protected static string $view = 'thiktak-filament-plugins::filament.pages.about-plugins-panels';
    protected static ?string $navigationGroup = 'Settings';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public function infolists(Infolist $infolist): Infolist
    {

        return $infolist
            ->state([])
            ->schema([

                Tabs::make('Panels')
                    ->tabs(function () {
                        $tabs = [];
                        foreach (filament()->getPanels() as $panelKey => $panel) {
                            //dd($panel);

                            $renderHooks = $this->getNonAccessibleProperty($panel, 'renderHooks');

                            $tabs[] = Tabs\Tab::make('Panel: ' . $panel->getId())
                                ->schema([
                                    Section::make('Informations')
                                        ->collapsible()
                                        ->aside(true)
                                        ->schema([
                                            TextEntry::make('id')
                                                ->getStateUsing($panel->getId()),

                                            TextEntry::make('path')
                                                ->getStateUsing($panel->getpath()),
                                        ]),

                                    Section::make('Theme')
                                        ->aside(true)
                                        ->schema([
                                            TextEntry::make('themeId')
                                                ->getStateUsing($panel->getTheme()->getId()),
                                            TextEntry::make('themePath')
                                                ->getStateUsing($panel->getTheme()->getpath()),

                                            IconEntry::make('hasDarkMode')
                                                ->getStateUsing($panel->hasDarkMode())
                                                ->boolean(),

                                            IconEntry::make('hasDarkModeForced')
                                                ->getStateUsing($panel->hasDarkModeForced())
                                                ->boolean(),
                                        ]),

                                    Section::make('Default Theme')
                                        ->aside(true)
                                        ->schema([
                                            TextEntry::make('defaultThemeId')
                                                ->getStateUsing($panel->getDefaultTheme()->getId()),
                                            TextEntry::make('defaultThemePath')
                                                ->getStateUsing($panel->getDefaultTheme()->getpath()),
                                        ]),

                                    Section::make('Pages')
                                        ->aside(true)
                                        ->schema([
                                            TextEntry::make('pagesDirectories')
                                                ->getStateUsing($panel->getPageDirectories())
                                                ->badge(),

                                            TextEntry::make('pageNamespaces')
                                                ->getStateUsing($panel->getPageNamespaces())
                                                ->badge(),

                                            TextEntry::make('pages')
                                                ->getStateUsing($panel->getPages())
                                                ->listWithLineBreaks()
                                                ->bulleted(),
                                        ]),

                                    Section::make('Widgets')
                                        ->aside(true)
                                        ->schema([
                                            TextEntry::make('widgetDirectories')
                                                ->getStateUsing($panel->getWidgetDirectories())
                                                ->badge(),

                                            TextEntry::make('widgetNamespaces')
                                                ->getStateUsing($panel->getWidgetNamespaces())
                                                ->badge(),

                                            TextEntry::make('widgets')
                                                ->getStateUsing($panel->getWidgets())
                                                ->listWithLineBreaks()
                                                ->bulleted(),
                                        ]),

                                    Section::make('Middleware')
                                        ->aside(true)
                                        ->schema([
                                            TextEntry::make('middlewares')
                                                ->getStateUsing($panel->getMiddleware())
                                                ->listWithLineBreaks()
                                                ->bulleted(),

                                            TextEntry::make('middlewaresAuth')
                                                ->getStateUsing($panel->getAuthMiddleware())
                                                ->listWithLineBreaks()
                                                ->bulleted(),

                                            TextEntry::make('middlewaresTenant')
                                                ->getStateUsing($panel->getTenantMiddleware())
                                                ->listWithLineBreaks()
                                                ->bulleted(),
                                        ]),

                                    Section::make('Hooks & Plugins')
                                        ->aside(true)
                                        ->schema([
                                            TextEntry::make('renderHooks')
                                                ->getStateUsing(collect($renderHooks)
                                                    ->map(function ($data, $key) {
                                                        return sprintf('%s (%s)', $key, count($data));
                                                    }))
                                                ->listWithLineBreaks()
                                                ->bulleted(),

                                            TextEntry::make('plugins')
                                                ->getStateUsing(collect($panel->getPlugins())
                                                    ->map(function ($data, $key) {
                                                        $rc = new \ReflectionClass($data);
                                                        $path = str_replace(base_path(), '', $rc->getFileName());
                                                        return new HtmlString("
    <div class=\"mb-1 p-1 block w-full\">
      <h6 class=\"block font-bold\">" . get_class($data) . "</h6>
      <div class=\"ps-2 truncate block w-full\">
        <em><small>" . $path . "</small></em>
      </div>
    </div>
");
                                                    }))
                                                ->html()
                                                ->listWithLineBreaks(),
                                        ]),
                                ]);
                        }
                        return $tabs;
                    })
                    ->activeTab(function () {
                        return array_search(
                            filament()->getCurrentPanel()->getId(),
                            array_keys(filament()->getpanels())
                        ) + 1;
                    }),
            ]);
    }

    public function unprotectProperty(Object $class, $property, $parentClass = null)
    {
        $class = new \ReflectionClass($parentClass ?: $class);
        $myProtectedProperty = $class->getProperty($property);
        $myProtectedProperty->setAccessible(true);
        return $myProtectedProperty->getValue($class);
    }

    public function getNonAccessibleProperty(Object $object, $property, $default = null)
    {
        $data = get_mangled_object_vars($object);
        return collect($data)
            ->mapWithKeys(function ($val, $key) {
                return [trim($key, "\x00*#-+") => $val];
            })
            ->get($property, $default);
    }

    public function getNonAccessibleProperties(Object $object)
    {
        $data = get_mangled_object_vars($object);
        return collect($data)
            ->mapWithKeys(function ($val, $key) {
                return [trim($key, "\x00*#-+") => $val];
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
/*
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
            $exception = new \Exception($cmd . " - " . $processOutput);
            report($exception);

            throw $exception;
        }

        return $processOutput;
    }
}
*/