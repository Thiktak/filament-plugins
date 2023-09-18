<?php

namespace Thiktak\FilamentPlugins\Filament\Pages;

use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Pages\Page;
use Thiktak\FilamentPlugins\Helpers\ComparePackages;

class AboutPluginsDashboard extends Page implements HasInfolists
{
    use InteractsWithInfolists;

    protected static string $view = 'thiktak-filament-plugins::filament.pages.about-plugins-dashboard';

    protected static ?string $navigationGroup = 'Settings';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public function getViewData(): array
    {
        $packagesData = ComparePackages::updateCaches();
        $packages = collect($packagesData['data']);

        return [
            'packages' => $packages,
            'pacakgesnutd' => $packages->where('compare', '<'),
            'panels' => collect(filament()->getPanels()),
        ];
    }
}
