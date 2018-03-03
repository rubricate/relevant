<?php

/*
 * @package     RubricatePHP
 * @author      Estefanio NS <estefanions AT gmail DOT com>
 * @link        http://rubricate.github.io
 * @copyright   2017 
 * 
 */

namespace Rubricate\Init;

class VObjectInit
{
    private $namespace;
    private $controller;
    private $action;
    private $param;
    private $controllerSuffix;
    private $actionSuffix;
    private $namespaceInController = array();



    public function __construct(
        $namespace,
        $controllerName,
        $actionName,
        $param
    ) {
        $this->namespace    = $namespace;
        $this->controller   = $controllerName;
        $this->action       = $actionName;
        $this->param        = $param;
    }



    public function getController($name = null)
    {
        $this->controller = (!is_null($name))
            ? $name: $this->controller;

        $ex = self::explodeController();

        $subSuffix  = ucfirst($ex[0]);


        $controller = (self::isNamespaceInController())
            ? $this->controller . $subSuffix
            : $this->controller;

        return ''
            . $this->namespace 
            . $controller 
            . $this->controllerSuffix
            . '';
    } 



    public function getAction($name = null)
    {
        $this->action = (!is_null($name))
            ? $name: $this->action;

        return $this->action . $this->actionSuffix;
    } 



    public function getParam()
    {
        return $this->param;
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
        if(is_array($name)) {

            foreach ($name as $value){
                self::addNamespaceInController($value);
            }

            return $this;
        }

        $this->namespaceInController[] = $name;

        return $this;
    } 




    public function isAction()
    {
        $c = self::getcontroller();
        $a = self::getAction();

        return  ( 
            method_exists($c, $a) && 
            method_exists($c, '__call')
        );
    } 




    private function isNamespaceInController()
    {
        $explode = self::explodeController();

        return (
            (count($explode) > 1) && 
            (in_array(ucfirst($explode[0]), $explode))
        );

    } 



    private function explodeController()
    {
        return explode('\\', $this->controller);
    } 




}    

