<?php

declare(strict_types=1);

namespace Combindma\Cmi\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Combindma\Cmi\Cmi
 */
class Cmi extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Combindma\Cmi\Cmi::class;
    }
}
