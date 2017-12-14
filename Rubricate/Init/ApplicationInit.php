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


class ApplicationInit implements IApplicationInit{

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




    
    public function addNamespaceInController($ns)
    {
        if(is_array($ns)){

            foreach ($ns as $value){
                self::addNamespaceInController($value);
            }

            return $this;
        }

        $this->namespaceInController[] = $ns;

        return $this;
    } 





    public function run() 
    {
        $controller = (self::isNamespaceInController())? ''
            . $this->controller 
            . self::getNamespaceInController(): ''
            . $this->controller 
            . '';


        $this->controller = ''
            . $this->controllerNamespace->get() 
            . $controller
            . $this->controllerSuffix
            . '' ;


        $this->action = ''
            . $this->action 
            . $this->actionSuffix
            . '';


        if ( !self::hasController() || self::hasNotAction() ){
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
            . 'Error404'
            . $this->controllerSuffix
            . '' ;


        if (!self::hasController()){
            exit('Page Not found');
        }


        $this->controller = new $this->controller();
        $this->controller->{"index" . $this->actionSuffix}();
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





    private function isNamespaceInController()
    {
        $e = explode('\\', $this->controller);
        $i = (count($e) > 1);
        $a = (in_array(ucfirst($e[0]), $e));

        return ($i && $a);
    } 





    private function getNamespaceInController()
    {
       $e = explode('\\', $this->controller);

       return ucfirst($e[0]);
    } 





}

