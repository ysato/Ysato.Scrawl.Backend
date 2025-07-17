<?php

declare(strict_types=1);

namespace Tests\Extensions;

use PHPUnit\Event\Application\Finished;
use PHPUnit\Event\Application\FinishedSubscriber;
use Tests\Support\OpenApiTracker;

/**
 * PHPUnit実行終了時にOpenAPI実装状況を表示するサブスクライバー
 */
class OpenApiStatusSubscriber implements FinishedSubscriber
{
    public function notify(Finished $event): void
    {
        // シングルトンパターンでOpenApiTrackerインスタンスを取得
        $tracker = OpenApiTracker::getInstance();

        // 実装状況が記録されている場合のみ表示
        if (! $tracker->hasImplementedEndpoints()) {
            return;
        }

        echo "\n\n=== OpenAPI Specification Coverage ===\n";
        $tracker->displayImplementationStatus();
    }
}
