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
    private array $namespaceInController = [];
    private bool $enableDirSubSufixController = false;
    private string|array|null $nameControllerError = null;
    private string $controllerSuffix = '';
    private string $actionSuffix = '';

    public function __construct(
        private readonly IControllerNamespaceRelevant $controllerNamespace,
        private readonly IUri $uri
    ) { }

    protected abstract function run(): void;

    public function setControllerSuffix(string $controllerSuffix): static
    {
        $this->controllerSuffix = $controllerSuffix;
        return $this;
    } 

    public function setActionSuffix(string $actionSuffix): static
    {
        $this->actionSuffix = $actionSuffix;
        return $this;
    } 

    public function setControllerNotFound(array|string $error404): static
    {
        $this->nameControllerError = $error404;
        return $this;
    }

    public function enableDirSubSufixController(): static
    {
        $this->enableDirSubSufixController = true;
        return $this;
    } 

    public function addNamespaceInController(array $name): static
    {
        foreach ($name as $value) {
            $this->namespaceInController[] = (string) $value;
        }
        return $this;
    }

    protected function getController(?string $name = null): string
    {
        $controller = $name ?? $this->uri->getNamespaceAndController();
        $subDir = '';

        if ($this->enableDirSubSufixController) {
            $dirArr = explode('\\', $controller);

            if (count($dirArr) > 1) {
                array_pop($dirArr);
                $subDir = implode('', $dirArr);
            }
        }

        return $this->controllerNamespace->get()
            . $controller
            . $subDir
            . $this->controllerSuffix;
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
        $c = $this->getController();
        $a = $this->getAction();

        return class_exists($c) && (method_exists($c, $a) || method_exists($c, '__call'));
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
            $this->controllerNamespace->get(),
            '',
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

