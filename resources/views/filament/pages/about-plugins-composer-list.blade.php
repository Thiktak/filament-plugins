<x-filament-panels::page>

    @include('thiktak-filament-plugins::filament.pages._menu')

    <div class="flex justify-between">
        <div>
            <x-filament::input.wrapper suffix-icon="heroicon-m-magnifying-glass">
                <x-filament::input type="search" wire:model.live="search" />
            </x-filament::input.wrapper>
        </div>
        <div>
            Showing <strong>{{ count($packages) }}</strong> of <strong>{{ $total }}</strong> results
        </div>
        <p>Last check : {{ $lastModified->diffForHumans() }}</p>
    </div>

    @php $nutd = $packages->where('compare', '<') @endphp
    @if ($nutd->count())
        <x-filament::section color="warning" icon="heroicon-o-exclamation-triangle" icon-color="danger">
            <x-slot name="heading" class="dark:text-black">
                Alerte
            </x-slot>
            You have {{ $nutd->count() }} packages not updated. Please run `composer update`
        </x-filament::section>
    @endif

    <div class="grid grid-cols-3 gap-3">
        @forelse ($packages->sortBy('compare') as $package)
            @php
                
                $v1 = $package['data']->getPrettyVersion();
                $v2 = $package['dist']->get('version');
                
                $vers = $v1 == $v2 ? $v1 : sprintf('%s %s %s', $v1, $package['compare'], $v2);
                
                $isFilament =
                    collect($package['data']->getKeywords())
                        ->intersect(['filament', 'filament-plugin'])
                        ->count() > 0 || Str::contains($package['data']->getName(), 'filament');
                
            @endphp
            <div
                class="relative bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
                <div class="p-2 !pb-0 --absolute top-2 right-2 left-2 flex justify-between">
                    <div class="flex gap-1">
                        <x-filament::badge :color="match (($stability = $package['data']->getStability())) {
                            'stable' => 'success',
                            'alpha' => 'warning',
                            'dev' => 'info',
                            default => 'gray',
                        }">
                            {{ $stability }}
                        </x-filament::badge>
                    </div>
                    <div class="flex gap-1">
                        @if ($isFilament)
                            <x-filament::badge color="warning">
                                Filament
                            </x-filament::badge>
                        @endif
                        <x-filament::badge
                            color="{{ $color = in_array($package['compare'], ['<']) ? 'danger' : 'success' }}">
                            {{ $vers }}
                        </x-filament::badge>
                    </div>
                </div>
                <div class="m-2 bg-no-repeat bg-contain bg-center bg-gray-200 rounded ring-1 ring-gray-200"
                    style="display: block; height: 12em; background-image: url({{ $package['image'] }});">
                    {{-- <img class="rounded-t-lg" src="/docs/images/blog/image-1.jpg" alt="" /> --}}
                </div>
                <div class="p-2">
                    <div class="px-1 mb-2 block" style="overflow: hidden; height: 4rem;">
                        <h5 class="text-xl font-bold tracking-tight text-gray-900 dark:text-white">
                            {{ $package['data']->getName() }}
                        </h5>
                    </div>
                    <div class="px-1" style="overflow: hidden; height: 6rem;">
                        <p class="font-normal text-gray-700 dark:text-gray-400">
                            {{ $package['data']->getDescription() }}
                        </p>
                    </div>
                    <div class="grid grid-cols-4 gap-2 mt-3 justify-between items-center">
                        @php
                            $links = [];
                            $links['github'] = str_replace('.git', '', $package['data']->getSourceUrl());
                            $links['homepage'] = $package['dist']['homepage'] ?: $package['data']->getHomepage();
                            $links = [...$links, ...$package['data']->getSupport()];
                            if ($isFilament) {
                                $links['filament'] = 'https://filamentphp.com/plugins';
                            }
                        @endphp

                        @foreach ($links as $kind => $link)
                            <x-filament::link size="xs" :icon="match ($kind) {
                                'issues' => 'heroicon-s-bug-ant',
                                'homepage' => 'heroicon-s-globe-europe-africa',
                                'doc' => 'heroicon-o-document-text',
                                'source' => 'heroicon-o-code-bracket',
                                'filament' => 'heroicon-s-heart',
                                default => 'heroicon-o-link',
                            }" color="gray" :href="$link"
                                target="_blank">
                                {{ $kind }}
                            </x-filament::link>
                        @endforeach
                    </div>
                    <div class="mt-3 text-xs">
                        Tags: {{ collect($package['data']->getKeywords())->implode(', ') }}
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-3 p-5">
                There is nothing here :(
            </div>
        @endforelse
    </div>

</x-filament-panels::page>
