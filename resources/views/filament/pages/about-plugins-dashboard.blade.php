<x-filament-panels::page>

    @include('thiktak-filament-plugins::filament.pages._menu')

    <div class="grid grid-cols-6 gap-4">
        <div class="col-span-4">
            <h4 class="font-bold my-2">Packages</h4>
            <div class="grid grid-cols-4 gap-4">
                <a href="{{ $url = \Thiktak\FilamentPlugins\Filament\Pages\AboutPluginsComposerList::getUrl() }}">
                    <x-filament::section>
                        <div class="flex items-center h-full">
                            <div class="text-4xl font-bold text-blue-500">{{ $packages->count() }}</div>
                            <div class="p-1">packages</div>
                        </div>
                    </x-filament::section>
                </a>

                <a href="{{ $url = \Thiktak\FilamentPlugins\Filament\Pages\AboutPluginsComposerList::getUrl() }}">
                    <x-filament::section>
                        <div
                            class="flex items-center h-full {{ $pacakgesnutd->count() > 0 ? 'text-danger-500' : 'text-success-500' }}">
                            <div class="text-4xl font-bold">{{ $pacakgesnutd->count() }}</div>
                            <div class="p-1">not up to date</div>
                        </div>
                    </x-filament::section>
                </a>
            </div>

            <br />

            <h4 class="font-bold my-2">Panels</h4>
            <div class="grid grid-cols-4 gap-4">
                <x-filament::section>
                    <div class="flex items-center h-full">
                        <div class="text-4xl font-bold text-blue-500">{{ $panels->count() }}</div>
                        <div class="p-1 py-4">panels</div>
                    </div>
                </x-filament::section>

                @foreach ($panels as $panel)
                    <x-filament::section>
                        <div class="flex items-center h-full">
                            <div class="text-4xl font-bold text-blue-500">{{ count($panel->getPlugins()) }}</div>
                            <div class="p-1">
                                <small>#{{ $panel->getId() }}</small>
                                <br />
                                plugins
                            </div>
                        </div>
                    </x-filament::section>
                @endforeach
            </div>
        </div>


        <x-filament::section class="col-span-2">
            <x-slot name="heading">
                Todo List
            </x-slot>
            <pre>[ ] Improve design of 'Composer'
[ ] Add dashboard</pre>
        </x-filament::section>
    </div>

</x-filament-panels::page>
