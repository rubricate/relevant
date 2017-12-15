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

    private $controller;
    private $action;
    private $param;
    private $controllerNamespace;
    private $controllerSuffix;
    private $namespaceInController = array();
    private $actionSuffix;




    public function __construct(IControllerNamespaceInit $c) 
    {
        $u = Uri::getInstance();
        $n = new ControllerToNamespacesUri($u);

        $this->controller = $n->getController();
        $this->action     = $u->getAction();
        $this->param      = $u->getParamArr();

        $this->controllerNamespace = $c;
    }




    public function setControllerSuffix($controllerSuffix)
    {
        $this->controllerSuffix = $controllerSuffix;

        return $this;
    } 




    public function setActionSuffix($actionSuffix)
    {
        $this->actionSuffix = $actionSuffix;

        return $this;
    } 




    public function addNamespaceInController($name)
    {
        if(is_array($name)){

            foreach ($name as $value){
                self::addNamespaceInController($value);
            }

            return $this;
        }

        $this->namespaceInController[] = $name;

        return $this;
    } 




    public function run() 
    {
        $this->controller = self::getController($this->controller);
        $this->action     = self::getAction($this->action);

        if ( !self::isController() || self::isNotAction() ){
            self::controllerError404();
        }

        $controller       = new $this->controller();
        $controllerAction = array($controller, $this->action);

        call_user_func_array($controllerAction, $this->param);
    }




    private function controllerError404() 
    {
        $this->controller = self::getController('Error404');

        if (!self::isController()){
            exit('Page Not found');
        }


        $controller = new $this->controller();
        $controller->{self::getAction('index')}();

        return;
    }




    private function isController()
    {
        return class_exists($this->controller);
    } 




    private function isNotAction()
    {
        return  ( 
            !method_exists($this->controller, $this->action) && 
            !method_exists($this->controller, '__call')
        );
    } 




    private function isNamespaceInController()
    {
        $explode = self::getExplodeController();
        $isCount = (count($explode) > 1);
        $isValid = (in_array(self::getNamespaceInController(), $explode));

        return ($isCount && $isValid);
    } 




    private function getNamespaceInController()
    {
        return ucfirst(self::getExplodeController(0));
    } 




    private function getExplodeController($key = NULL)
    {
        $e = explode('\\', $this->controller);
        $k = (!array_key_exists($key, $e))? array(): $e[$key];

        return (!is_null($key) && is_int($key))? $k: $e;
    } 




    private function getController($controller)
    {
        $is         = self::isNamespaceInController();
        $namespace  = $this->controllerNamespace->get();
        $subSuffix  = self::getNamespaceInController();
        $suffix     = $this->controllerSuffix;
        $controller = ($is)? $controller . $subSuffix: $controller;

        return $namespace . $controller . $suffix;
    } 




    private function getAction($action)
    {
        return $action . $this->actionSuffix;
    } 




}

