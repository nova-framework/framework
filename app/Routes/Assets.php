<?php

use Nova\Http\Request;

use Carbon\Carbon;


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

    return $dispatcher->servePackageFile($namespace, $path, $request);
});

// Register the route for assets from Vendor.
$dispatcher->route('vendor/(:all)', function (Request $request, $path) use ($dispatcher)
{
    return $dispatcher->serveVendorFile($path, $request);
});

// Register the route for files from protected folder.
$dispatcher->route('files/(:any)/(:any)/(:all)', function (Request $request, $hash, $timestamp, $path) use ($dispatcher)
{
    $basePath = Config::get('routing.files.path', BASEPATH .'files');

    $validity = Carbon::now()->subMinutes(
        Config::get('routing.files.validity', 180) // In minutes.
    );

    $localHash = hash_hmac('sha256', $path .'|' .$timestamp .'|' .$request->ip(), Config::get('app.key'));

    if (! File::isDirectory($basePath) || ! hash_equals($hash, $localHash) || ($validity->timestamp > hexdec($timestamp))) {
        return Response::make('Forbidden', 403);
    }

    $path = $basePath .DS .str_replace('/', DS, $path);

    return $dispatcher->serve($path, $request);
});
