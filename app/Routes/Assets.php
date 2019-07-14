<?php

use Nova\Http\Request;


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
