<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Ysato\Catalyst\ValidatesOpenApiSpec;

abstract class TestCase extends \Tests\TestCase
{
    use RefreshDatabase;
    use ValidatesOpenApiSpec;
}
