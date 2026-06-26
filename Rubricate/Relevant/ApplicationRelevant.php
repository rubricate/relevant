<?php

declare(strict_types=1);

namespace Rubricate\Relevant;

use Exception;
use Rubricate\Uri\CoreUri;
use Rubricate\Relevant\ControllerNamespaceRelevant as ControllerNs;

class ApplicationRelevant extends AbstractApplicationRelevant
{
    private array $middlewareConfig = [];

    public function __construct(string $controllerNamespace, array $routes = [])
    {
        $c = new ControllerNs($controllerNamespace);
        $u = new CoreUri($routes);

        parent::__construct($c, $u);
    }

    public function setMiddlewareConfig(array $config): static
    {
        $this->middlewareConfig = $config;
        return $this;
    }

    public function run(): void
    {
        $controller = $this->getController();
        $action     = $this->getAction();
        $param      = $this->getParam();

        $isExcept = in_array(
            $controller,
            $this->middlewareConfig['except'] ?? [],
            true
        );

        $this->executeMiddlewares($this->middlewareConfig['global'] ?? []);

        if (!$isExcept) {
            foreach ($this->middlewareConfig['group'] ?? [] as $pattern => $middlewares) {
                if (str_contains($controller, $pattern)) {
                    $this->executeMiddlewares($middlewares);
                }
            }

            $controllerMiddlewares = $this->middlewareConfig['controllers'][$controller] ?? [];

            if (!empty($controllerMiddlewares)) {
                $this->executeMiddlewares($controllerMiddlewares);
            }
        }

        if (!$this->isHttpCode200()) {
            $this->controllerError404();
        }

        $initController       = new $controller();
        $initControllerAction = [$initController, $action];

        call_user_func_array($initControllerAction, $param);
    }

    private function executeMiddlewares(array $middlewares): void
    {
        foreach ($middlewares as $middlewareClass) {
            if (is_subclass_of($middlewareClass, IMiddleware::class)) {
                $middlewareClass::handle();
            } else {
                throw new Exception("The class {$middlewareClass} must implement the IMiddleware interface");
            }
        }
    }

    private function controllerError404(): void
    {
        $controller = $this->getController(
            $this->getNameControllerError()
        );

        if (!class_exists($controller)) {
            exit('Page not found.');
        }

        $error404 = new $controller();
        $error404->{$this->getAction('index')}();

        exit();
    }
}

