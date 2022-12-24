<?php

declare(strict_types=1);

namespace Combindma\Cmi;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class CmiServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('cmi-payment')
            ->hasConfigFile('cmi-payment')
            ->hasViews('cmi');
    }

    public function packageRegistered()
    {
        $this->app->bind('cmi', fn () => new Cmi());
    }
}
