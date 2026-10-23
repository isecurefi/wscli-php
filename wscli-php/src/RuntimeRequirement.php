<?php

declare(strict_types=1);

namespace IsecureFi\WsCli;

/** Matches the patched minor branches in the Composer requirements. */
final class RuntimeRequirement
{
    public const DESCRIPTION = 'PHP 8.3.36+, 8.4.27+, or 8.5.12+ (within those minor branches)';

    public static function isSupported(int $versionId): bool
    {
        return ($versionId >= 80336 && $versionId < 80400)
            || ($versionId >= 80427 && $versionId < 80500)
            || ($versionId >= 80512 && $versionId < 80600);
    }
}
