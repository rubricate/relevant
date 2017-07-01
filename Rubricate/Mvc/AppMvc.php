<?php

/*
 * @package     RubriccatePHP
 * @author      Estefanio NS <estefanions AT gmail DOT com>
 * @link        http://rubricate.github.io
 * @copyright   2010 - 2017 
 * 
 */


namespace Rubricate\Mvc;


use Rubricate\Uri\Uri;
use Rubricate\Uri\ControllerToNamespacesUri;


class AppMvc implements IAppMvc{

    private $controller;
    private $action;
    private $namespace;
    private $uri;


    public function __construct($namespaceController) 
    {
        self::setNamespace($namespaceController);

        $uri       = Uri::getInstance();
        $namespace = new ControllerToNamespacesUri($uri);

        $this->controller = $namespace->getController();
        $this->action     = $uri->getAction();
        $this->uri        = $uri;
    }





    public function run() 
    {

        $this->controller = self::getNamespace($this->controller);


        if ( !self::hasController() || self::hasNotAction() )
        {
            $this->controllerError404();
        }

        $controller = new $this->controller();

        call_user_func_array(
            array($controller, $this->action), 
            $this->uri->getParamArr() 
        );

    }







    private function controllerError404() 
    {

        $this->controller = self::getNamespace('Error404');

        if (!self::hasController()) 
        {
            exit('Page Not found');
        }

        $this->controller = new $this->controller();
        $this->controller->index();
        exit();
    }







    private function getNamespace($controller)
    {
        return $this->namespace . $controller;
    }






    private function setNamespace($namespace)
    {
        $namespace = str_replace('.', '\\', $namespace);
        $this->namespace = rtrim($namespace, '\\') . '\\' ;
        return $this;
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

