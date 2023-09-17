<?php

namespace Thiktak\FilamentPlugins;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Thiktak\FilamentPlugins\Filament\Pages;

class FilamentPluginsPlugin implements Plugin
{
    public function getId(): string
    {
        return 'filament-plugins';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->pages([
                Pages\AboutPluginsList::class,
                //AboutPluginsUpToDate::class,
            ]);
    }

    public function boot(Panel $panel): void
    {
        $panel
            ->userMenuItems([
                'about-plugins' => \Filament\Navigation\MenuItem::make()
                    ->icon('heroicon-o-battery-100')
                    ->label('About plugins')
                    ->color('warning') //\Filament\Support\Colors\Color::Blue)
                    ->url(Pages\AboutPluginsList::getUrl())
                    ->sort(999),
                // ...
            ]);
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }
}
