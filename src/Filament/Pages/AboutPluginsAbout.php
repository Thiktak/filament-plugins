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
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\HtmlString;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use Thiktak\FilamentPlugins\Helpers\ComparePackages;
use Thiktak\FilamentPlugins\Vendors\Shadiakiki1986\ComposerWrapper;

class AboutPluginsAbout extends Page implements HasInfolists
{
    use InteractsWithInfolists;

    protected static string $view = 'thiktak-filament-plugins::filament.pages.about-plugins-about';
    protected static ?string $navigationGroup = 'Settings';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public function getViewData(): array
    {
        Artisan::call('about');
        $output = Artisan::output();

        $output = preg_replace('`([A-Za-z0-9+])([\. ]{2,})([A-Za-z0-9+])`', '$1[p]$2[/p]$3', $output);
        $output = preg_replace('`^(.*)(\.)([\n\r])$`m', '<span style="color: red">$1</span>', $output);

        $output = str_replace('[p]', '<span style="opacity: .25">', $output);
        $output = str_replace('[/p]', '</span>', $output);

        return [
            'output' => $output,
        ];
    }
}
