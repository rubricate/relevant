<?php

/*
 * @package     RubricatePHP
 * @author      Estefanio NS <estefanions AT gmail DOT com>
 * @link        http://rubricate.github.io
 * @copyright   2018 - 2019
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
    private $namespaceInController       = array();
    private $enableDirSubSufixController = false;
    private $nameControllerError         = null;



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



    public function setControllerError404($error404)
    {
       $this->nameControllerError = $error404;

       return $this;
    }



    public function enableDirSubSufixController()
    {
        $this->enableDirSubSufixController = true;

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

        $subDir = '';

        if($this->enableDirSubSufixController){

            $dirArr = explode('\\', $controller);

            if(count($dirArr) > 1){

                array_pop($dirArr);
                $subDir = implode('', $dirArr);
            }

        }

        return ''
            . $this->controllerNamespace->get()
            . $controller . $subDir
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



    protected function getNameControllerError()
    {
        $error404        = null;
        $subDir          = '/';

        if (!is_null($this->nameControllerError) ) {

            $error404       = $this->nameControllerError;
            $nameController = null;

            if(is_array($error404)) {

                $c = self::getController();

                $e = explode('\\', $c);

                if(count($e) > 3){
                    $subDir = $e[2];
                }

                if(in_array($subDir, array_keys($error404))){

                    $nameController = $error404[$subDir];
                }

            }

            $error404 = $nameController;
        }

        return $error404;
    }



}    

