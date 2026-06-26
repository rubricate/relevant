<?php

declare(strict_types=1);

namespace Rubricate\Relevant;

interface ISetControllerSuffixRelevant
{
    public function setControllerSuffix(string $suffix): static;
}

