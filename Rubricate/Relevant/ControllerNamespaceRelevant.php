<?php

declare(strict_types=1);

namespace Rubricate\Relevant;

class ControllerNamespaceRelevant implements IControllerNamespaceRelevant
{
    private readonly string $controllerNamespace;

    public function __construct(string $controllerNamespace)
    {
        $search = ['.', '-', '_'];
        $ns     = str_replace($search, '\\', $controllerNamespace);
        
        $this->controllerNamespace = rtrim($ns, '\\') . '\\';
    }

    public function get(): string
    {
        return $this->controllerNamespace;
    } 
}

