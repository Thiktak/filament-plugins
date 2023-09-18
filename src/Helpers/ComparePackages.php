<?php

namespace Thiktak\FilamentPlugins\Helpers;

/* @source https://github.com/shadiakiki1986/composer-wrapper */

use Illuminate\Support\Facades\Cache;
use Thiktak\FilamentPlugins\Vendors\Shadiakiki1986\ComposerWrapper;

// code below copied from https://github.com/composer/composer/blob/master/src/Composer/Command/ShowCommand.php
class ComparePackages
{
    public static function updateCaches()
    {

        return Cache::rememberForever('list-of-packages', function () { /*use ($installedPackages, $allDataPackagist) */

            $installedPackages = Cache::remember('composer-packages', 15 * 60, function () {
                $io = new \Composer\IO\NullIO();
                $factory = new \Composer\Factory();
                $composer = $factory->createComposer($io, app_path('../composer.json'));

                $cw = new ComposerWrapper($composer);
                $packages = $cw->showDirect();

                return $packages['<info>installed</info>:'] ?? [];
            });

            $allDataPackagist = collect();

            foreach ($installedPackages as $package) {
                $dataPackagist = json_decode(
                    $j = Cache::remember(md5($package->getname()), 15 * 60, function () use ($package) {
                        //echo 'FETCH ';
                        //if ($package->getName() == 'thiktak/filament-simple-list-entry') dd($package);

                        $return = file_get_contents('https://repo.packagist.org/p2/' . $package->getname() . '.json');
                        if (strlen($return) < 255) {
                            $return = file_get_contents('https://repo.packagist.org/p2/' . $package->getname() . '~dev.json');
                        }

                        return $return;
                    }),
                    JSON_PRETTY_PRINT
                );

                $allDataPackagist = $allDataPackagist->mergeRecursive($dataPackagist['packages']);
            }

            $return = [];

            foreach ($installedPackages as $packageName => $package) {

                $dist = collect(@$allDataPackagist[$package->getName()][0]);

                $versionPackage = trim(
                    $package->getPrettyVersion() . ' ' . ($package->isDev() ? substr($package->getSourceReference(), 0, 7) : null)
                );

                $versionDist = trim(
                    $dist->get('version') . ' ' . (strpos($dist->get('version'), 'dev') !== false ? substr(data_get($dist, 'source.reference'), 0, 7) : null)
                );

                /*echo '<div>';
                echo '<h2>', $package->getName(), '</h2>';
                echo 'Local: ', $versionPackage, ' - ', $package->getReleaseDate()->format('Y-m-d H:i:s'), '<br />';*/

                /*if ($package->getName() == 'thiktak/filament-plugins') {
                    dd($pacakge);
                }*/

                if ($package->getPrettyVersion() == $dist->get('version')) {
                    if ($package->getSourceReference() == data_get($dist, 'source.reference')) {
                        $compare = '=';
                        /*echo '=';*/
                    } else {
                        $compare = '<';
                        /*echo 'NOT UP TO DATE';*/
                    }
                } else {
                    /*echo version_compare($versionPackage, $versionDist, '<') ? '< (Update)' : '>= (ok)';*/
                    if (version_compare($versionPackage, $versionDist, '<')) {
                        $compare = '<';
                    } else {
                        $compare = '>=';
                    }
                }

                $url = str_replace('.git', '', $package->getSourceUrl());
                $return[$packageName] = [
                    'data' => $package,
                    'dist' => $dist,
                    'compare' => $compare,
                    'image' => collect(@get_meta_tags($url))
                        ->only('twitter:image:src', 'og:image')
                        ->first(),
                ];

                //dd($return); //collect($package)->toArray());

                /*echo '<br />';
                echo 'Dist: ', $versionDist, ' - ', $dist->get('time');
                echo '</div>';
                dump($dist);*/
            }

            return [
                'lastModified' => new \DateTime,
                'data' => $return,
            ];
        });
    }
}
