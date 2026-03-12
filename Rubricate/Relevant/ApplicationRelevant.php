<?php

declare(strict_types=1);

namespace Rubricate\Relevant;

use Rubricate\Uri;
use Rubricate\Relevant\ControllerNamespaceRelevant as ControllerNs;
use Rubricate\Relevant\IMiddleware;

class ApplicationRelevant extends AbstractApplicationRelevant
{
    private array $middlewareConfig = [];

    public function __construct(
        string $controllerNamespace, array $routes = []
    ) { 

        $c = new ControllerNs($controllerNamespace);
        $u = new Uri\CoreUri($routes);

        parent::__construct($c, $u);
    }

    public function setMiddlewareConfig(array $config): self
    {
        $this->middlewareConfig = $config;
        return $this;
    }

    public function run(): void
    {
        $controller   = parent::getController();
        $action       = parent::getAction();
        $param        = parent::getParam();

        $isExcept = in_array(
            $controller,
            $this->middlewareConfig['except'] ?? []
        );

        $this->executeMiddlewares($this->middlewareConfig['global'] ?? []);

        if (!$isExcept) {
            foreach ($this->middlewareConfig['group'] ?? [] as $pattern => $middlewares) {
                if (str_contains($controller, $pattern)) {
                    $this->executeMiddlewares($middlewares);
                }
            }
        }

        if (!parent::isHttpCode200()) {
            self::controllerError404();
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
                throw new \Exception(''
                    . "The class {$middlewareClass} "
                    . 'must implement the IMiddleware interface'
                );
            }
        }
    }


    private function controllerError404(): void
    {
        $controller = parent::getController(
            parent::getNameControllerError()
        );

        if (!class_exists($controller)) {
            exit('Page not found.');
        }

        $error404 = new $controller();
        $error404->{parent::getAction('index')}();

        exit();
    }

}

