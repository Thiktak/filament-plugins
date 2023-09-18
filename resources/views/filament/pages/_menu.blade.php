<x-filament::tabs>
    <x-filament::tabs.item :href="$url = \Thiktak\FilamentPlugins\Filament\Pages\AboutPluginsDashboard::getUrl()" tag="a" :active="request()->fullUrlIs($url)">
        Dashboard
    </x-filament::tabs.item>
    <x-filament::tabs.item :href="$url = \Thiktak\FilamentPlugins\Filament\Pages\AboutPluginsPanels::getUrl()" tag="a" :active="request()->fullUrlIs($url)">
        Appplication panels
        <x-slot name="badge">
            {{ count(filament()->getPanels()) }}
        </x-slot>
    </x-filament::tabs.item>
    <x-filament::tabs.item :href="$url = \Thiktak\FilamentPlugins\Filament\Pages\AboutPluginsComposerList::getUrl()" tag="a" :active="request()->fullUrlIs($url)">
        Composer
    </x-filament::tabs.item>
    <x-filament::tabs.item :href="$url = \Thiktak\FilamentPlugins\Filament\Pages\AboutPluginsAbout::getUrl()" tag="a" :active="request()->fullUrlIs($url)">
        Artisan about
    </x-filament::tabs.item>
    <x-filament::tabs.item :href="$url = \Thiktak\FilamentPlugins\Filament\Pages\AboutPluginsViews::getUrl()" tag="a" :active="request()->fullUrlIs($url)">
        Views
    </x-filament::tabs.item>
</x-filament::tabs>


<div id="alert-4"
    class="flex items-center p-4 mb-4 text-yellow-800 rounded-lg bg-yellow-50 dark:bg-gray-800 dark:text-yellow-300"
    role="alert">
    <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
        viewBox="0 0 20 20">
        <path
            d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
    </svg>
    <span class="sr-only">Info</span>
    <div class="ml-3 text-sm font-medium">
        <span class="font-medium">Warning alert!</span> This plugin is not yet ready for production. Please help us
        to improve it.
    </div>
    <x-filament::link size="xs" icon="heroicon-o-link" color="gray"
        href="https://github.com/Thiktak/filament-plugins/issues" target="_blank" class="ml-auto -mx-1.5 -my-1.5">
        Raise issue or comment
    </x-filament::link>
</div>
