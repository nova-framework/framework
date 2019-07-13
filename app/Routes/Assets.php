<?php

use Nova\Http\Request;
use Nova\Http\Response;


// Register the route for assets from main assets folder.
$dispatcher->route('assets/(:all)', function (Request $request, $path) use ($dispatcher)
{
    $basePath = Config::get('routing.assets.path', BASEPATH .'assets');

    $path = $basePath .DS .str_replace('/', DS, $path);

    return $dispatcher->serve($path, $request);
});

// Register the route for assets from Packages, Modules and Themes.
$dispatcher->route('packages/(:any)/(:any)/(:all)', function (Request $request, $vendor, $package, $path) use ($dispatcher)
{
    $namespace = $vendor .'/' .$package;

    if (is_null($packagePath = $dispatcher->getPackagePath($namespace))) {
        return new Response('File Not Found', 404);
    }

    $path = $packagePath .str_replace('/', DS, $path);

    return $dispatcher->serve($path, $request);
});

// Register the route for assets from Vendor.
$dispatcher->route('vendor/(:all)', function (Request $request, $path) use ($dispatcher)
{
    $paths = $dispatcher->getVendorPaths();

    if (! Str::startsWith($path, $paths)) {
        return new Response('File Not Found', 404);
    }

    $path = BASEPATH .'vendor' .DS .str_replace('/', DS, $path);

    return $dispatcher->serve($path, $request);
});
