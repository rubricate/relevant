<?php

declare(strict_types=1);

namespace Rubricate\Relevant;

use Rubricate\Relevant\IControllerNamespaceRelevant;
use Rubricate\Uri\IUri;

abstract class AbstractApplicationRelevant implements 

    ISetControllerSuffixRelevant,
    ISetActionSuffixRelevant,
    IAddNamespaceInControllerRelevant
{
    private array $namespaceInController      = [];
    private bool $enableDirSubSufixController = false;
    private ?string $nameControllerError      = null;

    public function __construct(
        private IControllerNamespaceRelevant $controllerNamespace,
        private IUri $uri
    ) { }

    protected abstract function run(): void;

    public function setControllerSuffix(string $controllerSuffix): static
    {
        $this->controllerSuffix = $controllerSuffix;
        return $this;
    } 

    public function setActionSuffix($actionSuffix): static
    {
        $this->actionSuffix = $actionSuffix;
        return $this;
    } 

    public function setControllerNotFound(array|string $error404): static
    {
        $this->nameControllerError = (is_array($error404))?
            json_encode($error404): $error404;
       return $this;
    }

    public function enableDirSubSufixController(): static
    {
        $this->enableDirSubSufixController = true;
        return $this;
    } 

    public function addNamespaceInController(array $name): static
    {
            foreach ((array) $name as $value){
                self::addNamespaceInController($value);
            }
            return $this;

    }

    protected function getController($name = null): string
    {
        $controller = $name ?? $this->uri->getNamespaceAndController();

        $subDir = '';

        if($this->enableDirSubSufixController){

            $dirArr = explode('\\', $controller);

            if(count($dirArr) > 1){

                array_pop($dirArr);
                $subDir = implode('', $dirArr);
            }

        }

        return ''
            . $this->controllerNamespace->get()
            . $controller . $subDir
            . $this->controllerSuffix
            . '';

    }

    protected function getAction(?string $name = null): string
    {
        $action = $name ?? $this->uri->getAction();
        return $action . $this->actionSuffix;
    }

    protected function getParam(): array
    {
        return $this->uri->getParamArr();
    }

    protected function isHttpCode200(): bool
    {
        $c = self::getController();
        $a = self::getAction();

        return
            class_exists($c) && (method_exists($c, $a) ||
            method_exists($c, '__call'));
    }

    protected function getNameControllerError(): string
    {
        if (is_null($this->nameControllerError)) {
            return '';
        }

        if (!is_array($this->nameControllerError)) {
            return $this->nameControllerError;
        }

        $controller = str_replace(
            $this->controllerNamespace->get(), '',
            $this->getController()
        );

        $segments = explode('\\', $controller);

        if (count($segments) > 1) {
            $subDir = $segments[0];
            return $this->nameControllerError[$subDir] ?? '';
        }

        return '';
    }

}    

