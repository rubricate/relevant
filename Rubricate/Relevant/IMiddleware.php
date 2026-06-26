<?php

declare(strict_types=1);

namespace Rubricate\Relevant;

interface IMiddleware
{
    public static function handle(): void;
}
