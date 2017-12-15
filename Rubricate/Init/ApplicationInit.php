<?php

/*
 * @package     RubricatePHP
 * @author      Estefanio NS <estefanions AT gmail DOT com>
 * @link        http://rubricate.github.io
 * @copyright   2010 - 2017 
 * 
 */

namespace Rubricate\Init;

use Rubricate\Uri\Uri;
use Rubricate\Uri\ControllerToNamespacesUri;


class ApplicationInit implements IApplicationInit
{


    public function __construct(IControllerNamespaceInit $namespace) 
    {
        $u = Uri::getInstance();
        $n = new ControllerToNamespacesUri($u);

        $this->vObject = new VObjectInit(
            $namespace->get(), $n->getController(),
            $u->getAction(),   $u->getParamArr()
        );
    }




    public function setControllerSuffix($controllerSuffix)
    {
        $this->vObject->setControllerSuffix($controllerSuffix);

        return $this;
    } 




    public function setActionSuffix($actionSuffix)
    {
        $this->vObject->setActionSuffix($actionSuffix);

        return $this;
    } 




    public function addNamespaceInController($name)
    {
        $this->vObject->addNamespaceInController($name);

        return $this;
    } 




    public function run() 
    {
        $controller = $this->vObject->getController();
        $action     = $this->vObject->getAction();
        $param      = $this->vObject->getParam();

        if ( 
            !class_exists($controller) || 
            $this->vObject->isAction() 
        ){
            self::controllerError404();
        }

        $initController       = new $controller();
        $initControllerAction = array($initController, $action);

        call_user_func_array($initControllerAction, $param);
    }




    private function controllerError404() 
    {
        $controller = $this->vObject->getController('Error404');

        if (!class_exists($controller)){
            exit('Page Not found');
        }

        $error404 = new $controller();
        $error404->{$this->vObject->getAction('index')}();

        exit();

    }




}

