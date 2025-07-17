<?php

declare(strict_types=1);

namespace Tests\Extensions;

use PHPUnit\Runner\Extension\Extension;
use PHPUnit\Runner\Extension\Facade;
use PHPUnit\Runner\Extension\ParameterCollection;
use PHPUnit\TextUI\Configuration\Configuration;

/**
 * OpenAPI実装状況を表示するPHPUnit拡張
 */
class OpenApiStatusExtension implements Extension
{
    public function bootstrap(
        Configuration $configuration,
        Facade $facade,
        ParameterCollection $parameters,
    ): void {
        $facade->registerSubscriber(
            new OpenApiStatusSubscriber(),
        );
    }
}
