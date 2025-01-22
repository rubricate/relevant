<?php

declare(strict_types=1);

namespace Rubricate\Relevant;

use Rubricate\Uri;
use Rubricate\Relevant\ControllerNamespaceRelevant as ControllerNs;

class ApplicationRelevant extends AbstractApplicationRelevant
{
    public function __construct(
        string $controllerNamespace, array $routes = []
    ) { 

        $c = new ControllerNs($controllerNamespace);
        $u = new Uri\CoreUri($routes);

        parent::__construct($c, $u);
    }

    public function run(): void
    {
        $controller   = parent::getController();
        $action       = parent::getAction();
        $param        = parent::getParam();

        if (!parent::isHttpCode200()) {
            self::controllerError404();
        }

        $initController       = new $controller();
        $initControllerAction = array($initController, $action);

        call_user_func_array($initControllerAction, $param);
    }

    private function controllerError404(): void
    {
        $controller = parent::getController(
            parent::getNameControllerError()
        );

        if (!class_exists($controller)) {
            exit('Page Not found');
        }

        $error404 = new $controller();
        $error404->{parent::getAction('index')}();

        exit();
    }

}

