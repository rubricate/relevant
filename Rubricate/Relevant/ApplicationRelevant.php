<?php

/*
 * @package     RubricatePHP
 * @author      Estefanio NS <estefanions AT gmail DOT com>
 * @link        http://rubricate.github.io
 * @copyright   2010 - 2018 
 * 
 */

namespace Rubricate\Relevant;

use Rubricate\Uri\IUri;


class ApplicationRelevant extends AbstractApplicationRelevant
{


    public function __construct(
        IControllerNamespaceRelevant $c, IUri $u
    ) { 
        parent::__construct($c, $u);
    }





    public function run() 
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




    private function controllerError404() 
    {
        $controller = parent::getController('Error404');

        if (!class_exists($controller)) {
            exit('Page Not found');
        }

        $error404 = new $controller();
        $error404->{parent::getAction('index')}();

        exit();

    }




}

