<?php

/*
 * @package     RubricatePHP
 * @author      Estefanio NS <estefanions AT gmail DOT com>
 * @link        http://rubricate.github.io
 * @copyright   2018 
 * 
 */

namespace Rubricate\Relevant;

use Rubricate\Relevant\IControllerNamespaceRelevant;
use Rubricate\Uri\IUri;

abstract class AbstractApplicationRelevant implements 

    ISetControllerSuffixRelevant,
    ISetActionSuffixRelevant,
    IAddNamespaceInControllerRelevant
{
    private $controllerNamespace;
    private $uri;
    private $controllerSuffix;
    private $actionSuffix;
    private $namespaceInController = array();



    public function __construct(
        IControllerNamespaceRelevant $c, IUri $u
    ) { 
        $this->controllerNamespace = $c;
        $this->uri                 = $u;
    }


    protected abstract function run();




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




    protected function getController($name = null)
    {
        $controller = (!is_null($name))
            ? $name: $this->uri->getNamespaceAndController();

        $explode   = self::explodeController();
        $subSuffix = ucfirst($explode[0]);


        $controller = (self::isNamespaceInController())
            ? $controller . $subSuffix
            : $controller;

        return ''
            . $this->controllerNamespace->get()
            . $controller 
            . $this->controllerSuffix
            . '';
    } 




    protected function getAction($name = null)
    {
        $is      = (is_null($name));
        $default = $this->uri->getAction();
        $action  = (!$is) ? $name: $default;

        return $action . $this->actionSuffix;
    } 




    protected function getParam()
    {
        return $this->uri->getParamArr();
    } 




    protected function isHttpCode200()
    {
        $c = self::getController();
        $a = self::getAction();

        $isAction = method_exists($c, $a);
        $isCall   = method_exists($c, '__call');

        if( !class_exists( self::getController() ) ) {
            return false;
        }

        return ($isAction || $isCall);
    } 




    private function isNamespaceInController()
    {
        $explode = self::explodeController();
        $isCount = (count($explode) > 1);
        $inArr   = (in_array(ucfirst($explode[0]), $explode));

        return ($isCount && $inArr);

    } 






    private function explodeController()
    {
        return explode('\\', $this->uri->getController());
    } 




}    

