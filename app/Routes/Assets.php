<?php

use Nova\Http\Request;


// Register the route for assets from main assets folder.
$dispatcher->route('assets/(:all)', function (Request $request, $path) use ($dispatcher)
{
    $basePath = Config::get('routing.assets.path', BASEPATH .'assets');

    return $basePath .DS .str_replace('/', DS, $path);
});

// Register the route for assets from Packages, Modules and Themes.
$dispatcher->route('packages/(:any)/(:any)/(:all)', function (Request $request, $vendor, $package, $path) use ($dispatcher)
{
    if (empty($basePath = $dispatcher->getPackagePath($vendor, $package))) {
        return Response::make('File Not Found', 404);
    }

    return $basePath .DS .str_replace('/', DS, $path);
});

// Register the route for assets from Vendor.
$dispatcher->route('vendor/(:all)', function (Request $request, $path) use ($dispatcher)
{
    $basePath = BASEPATH .'vendor' .DS;

    if (! Str::startsWith($path, $dispatcher->getVendorPaths())) {
        return Response::make('File Not Found', 404);
    }

    return $basePath .DS .str_replace('/', DS, $path);
});
