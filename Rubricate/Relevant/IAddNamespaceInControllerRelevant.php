<?php

declare(strict_types=1);

namespace Rubricate\Relevant;

interface IAddNamespaceInControllerRelevant
{
    public function addNamespaceInController(array $name): static;
}

