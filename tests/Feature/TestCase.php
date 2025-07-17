<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\TracksOpenApiImplementation;

abstract class TestCase extends \Tests\TestCase
{
    use RefreshDatabase;
    use TracksOpenApiImplementation;
}
