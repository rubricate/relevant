<?php

declare(strict_types=1);

namespace Rubricate\Relevant;

class TargetNamespaceRelevant implements ITargetNamespaceRelevant
{
    private readonly string $namespace;

    public function __construct(string $namespace)
    {
        $search = ['.', '-', '_'];
        $n = str_replace($search, '\\', $namespace);
        
        $this->namespace = rtrim($n, '\\') . '\\';
    }

    public function get(): string
    {
        return $this->namespace;
    } 
}

