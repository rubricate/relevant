<?php

/*
 * @package     RubriccatePHP
 * @author      Estefanio NS <estefanions AT gmail DOT com>
 * @link        http://rubricate.github.io
 * @copyright   2010 - 2017 
 * 
 */


namespace Rubricate\Kernel;


use Rubricate\Uri\Uri;
use Rubricate\Uri\ControllerToNamespacesUri;


class ApplicationKernel implements IApplicationKernel{

    private $controller;
    private $action;
    private $param;
    private $controllerNamespace;


    public function __construct(IControllerNamespaceKernel $c) 
    {
        $u = Uri::getInstance();
        $n = new ControllerToNamespacesUri($u);

        $this->controller = $n->getController();
        $this->action     = $u->getAction();
        $this->param      = $u->getParamArr();

        $this->controllerNamespace = $c;
    }





    public function run() 
    {
        $this->controller = ''
            . $this->controllerNamespace->get() 
            . $this->controller;


        if ( !self::hasController() || self::hasNotAction() )
        {
            $this->controllerError404();
        }

        $controller       = new $this->controller();
        $controllerAction = array($controller, $this->action);

        call_user_func_array($controllerAction, $this->param);
    }







    private function controllerError404() 
    {

        $this->controller = ''
            . $this->controllerNamespace->get() 
            . 'Error404';

        if (!self::hasController()) 
        {
            exit('Page Not found');
        }

        $this->controller = new $this->controller();
        $this->controller->index();
        exit();
    }







    private function hasController()
    {
        return class_exists($this->controller);
    } 





    private function hasNotAction()
    {
        return  ( 
            !method_exists($this->controller, $this->action) && 
            !method_exists($this->controller, '__call')
        );
    } 



}

