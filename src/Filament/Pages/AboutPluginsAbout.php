<?php

namespace Thiktak\FilamentPlugins\Filament\Pages;

use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Artisan;

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
