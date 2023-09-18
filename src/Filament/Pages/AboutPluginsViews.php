<?php

namespace Thiktak\FilamentPlugins\Filament\Pages;

use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Pages\Page;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\HtmlString;

class AboutPluginsViews extends Page implements HasInfolists
{
    use InteractsWithInfolists;

    protected static string $view = 'thiktak-filament-plugins::filament.pages.about-plugins-views';

    protected static ?string $navigationGroup = 'Settings';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public function infolists(Infolist $infolist): infolist
    {
        $finder = app('view')->getFinder();
        /*dd(
            $view = view($v = 'thiktak-filament-plugins::filament.pages.about-plugins-views'),
            $view->getPath(),
            app('cache'),
            Cache::get('a'),
            //new \Illuminate\Cache\FileStore(),
            $key = Cache::store('file')->path($view->getPath()),
            Cache::get($v),
            Cache::get($view->getPath()),
            Cache::get($key),
            $key = Cache::store('file')->path($v),
            Cache::get($key),
            dd(Cache::store('file')),
            Cache::store('file')->get('S:\Devs\xampp\htdocs\www\projects\p101_lv10_matrix\vendor\filament\support\src\/../resources/views/components/badge.blade.php')
            //Cache::store('file')->get($view),
        );
        dd(app('cache'), Cache::get('thiktak-filament-plugins::filament.pages.about-plugins-views'));*/

        return $infolist
            ->schema([

                Section::make('View paths')
                    ->description('All paths scanned for views')
                    ->aside(true)
                    ->schema([
                        TextEntry::make('viewFinderPaths')
                            ->getStateUsing($finder->getPaths())
                            ->listWithLineBreaks()
                            ->bulleted(),
                    ]),

                Section::make('View Hints')
                    ->aside(true)
                    ->schema([
                        TextEntry::make('viewHints')
                            ->getStateUsing(collect($finder->getHints())
                                ->map(function ($data, $key) {
                                    return new HtmlString('
<div class="mb-1 p-1 block w-full">
<h6 class="block font-bold">' . $key . '</h6>
<ul class="ps-2 list-inside list-disc">
' . collect($data)
                                        ->map(function ($path) {
                                            $path = str_replace(base_path(), '', $path);

                                            return '<li><em><small>' . $path . '</small></em></li>';
                                        })
                                        ->implode('') . '
</ul>
</div>
');
                                }))
                            ->listWithLineBreaks()
                            ->bulleted(),
                    ]),

                Section::make('View Hints')
                    ->aside(false)
                    ->schema(
                        fn () => collect($finder->getHints())
                            ->filter(fn ($val, $key) => $key != '__components')
                            ->map(function ($hintData, $hintKey) {
                                return Section::make($hintKey)
                                    ->aside(true)
                                    ->schema(
                                        fn () => collect($hintData)
                                            ->map(function ($hintSrc) {
                                                $path = fn ($path, $src = null) => str_replace($src ?: base_path(), '', $path);

                                                return Section::make(self::remove_dot_segments($path($hintSrc)))
                                                    ->collapsible()
                                                    ->schema([
                                                        TextEntry::make('')
                                                            ->getStateUsing(
                                                                collect(\Illuminate\Support\Facades\File::allFiles($hintSrc))
                                                                    ->map(fn ($file) => $path($file->getPathName(), $hintSrc))
                                                                    ->toArray()
                                                            )
                                                            ->listWithLineBreaks()
                                                            ->bulleted(),
                                                    ]);
                                            })
                                            ->toArray()
                                    );
                            })
                            ->toArray()
                    ),

            ]);
    }

    // @source https://stackoverflow.com/questions/21421569/sanitize-file-path-in-php-without-realpath
    // as per RFC 3986
    // @see https://www.rfc-editor.org/rfc/rfc3986#section-5.2.4
    public static function remove_dot_segments($input)
    {
        $input = str_replace('\\', '/', $input); // Georges: add

        // 1.  The input buffer is initialized with the now-appended path
        //     components and the output buffer is initialized to the empty
        //     string.
        $output = '';

        // 2.  While the input buffer is not empty, loop as follows:
        while ($input !== '') {
            // A.  If the input buffer begins with a prefix of "`../`" or "`./`",
            //     then remove that prefix from the input buffer; otherwise,
            if (
                ($prefix = substr($input, 0, 3)) == '../' ||
                ($prefix = substr($input, 0, 2)) == './'
            ) {
                $input = substr($input, strlen($prefix));
            } elseif // B.  if the input buffer begins with a prefix of "`/./`" or "`/.`",
            //     where "`.`" is a complete path segment, then replace that
            //     prefix with "`/`" in the input buffer; otherwise,
            (
                ($prefix = substr($input, 0, 3)) == '/./' ||
                ($prefix = $input) == '/.'
            ) {
                $input = '/' . substr($input, strlen($prefix));
            } elseif // C.  if the input buffer begins with a prefix of "/../" or "/..",
            //     where "`..`" is a complete path segment, then replace that
            //     prefix with "`/`" in the input buffer and remove the last
            //     segment and its preceding "/" (if any) from the output
            //     buffer; otherwise,
            (
                ($prefix = substr($input, 0, 4)) == '/../' ||
                ($prefix = $input) == '/..'
            ) {
                $input = '/' . substr($input, strlen($prefix));
                $output = substr($output, 0, strrpos($output, '/'));
            } elseif // D.  if the input buffer consists only of "." or "..", then remove
            //     that from the input buffer; otherwise,
            ($input == '.' || $input == '..') {
                $input = '';
            } else { // E.  move the first path segment in the input buffer to the end of
                //     the output buffer, including the initial "/" character (if
                //     any) and any subsequent characters up to, but not including,
                //     the next "/" character or the end of the input buffer.
                $pos = strpos($input, '/');
                if ($pos === 0) {
                    $pos = strpos($input, '/', $pos + 1);
                }
                if ($pos === false) {
                    $pos = strlen($input);
                }
                $output .= substr($input, 0, $pos);
                $input = (string) substr($input, $pos);
            }
        }

        // 3.  Finally, the output buffer is returned as the result of remove_dot_segments.
        $output = str_replace('/', DIRECTORY_SEPARATOR, $output); // Georges: add

        return $output;
    }
}
