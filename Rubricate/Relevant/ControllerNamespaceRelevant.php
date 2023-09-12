<?php

declare(strict_types=1);

namespace Rubricate\Relevant;

class ControllerNamespaceRelevant implements IControllerNamespaceRelevant
{
    private $controllerNamespace;

    public function __construct($controllerNamespace)
    {
        self::init($controllerNamespace);
    }

    public function get(): string
    {
        return $this->controllerNamespace;
    } 

    private function init($controllerNamespace): object
    {
        $search = array('.', '-', '_');
        $ns = str_replace($search, '\\', $controllerNamespace);
        $this->controllerNamespace = rtrim($ns, '\\') . '\\' ;

        return $this;
    } 

}    

