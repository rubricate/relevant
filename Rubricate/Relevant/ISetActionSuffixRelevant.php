<?php

declare(strict_types=1);

namespace Rubricate\Relevant;

interface ISetActionSuffixRelevant
{
    public function setActionSuffix(string $suffix): static;
}

