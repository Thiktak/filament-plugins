<x-filament-panels::page>

    @foreach ($packages as $package)
        <div>{{ $package }}</div>
    @endforeach

</x-filament-panels::page>
