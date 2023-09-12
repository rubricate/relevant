<?php

declare(strict_types=1);

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
    private $namespaceInController       = [];
    private $enableDirSubSufixController = false;
    private $nameControllerError         = null;

    public function __construct(
        IControllerNamespaceRelevant $c, IUri $u
    ) { 
        $this->controllerNamespace = $c;
        $this->uri                 = $u;
    }

    protected abstract function run();

    public function setControllerSuffix($controllerSuffix): object
    {
        $this->controllerSuffix = $controllerSuffix;

        return $this;
    } 

    public function setActionSuffix($actionSuffix): object
    {
        $this->actionSuffix = $actionSuffix;

        return $this;
    } 

    public function setControllerNotFound($error404): object
    {
       $this->nameControllerError = $error404;

       return $this;
    }

    public function enableDirSubSufixController(): object
    {
        $this->enableDirSubSufixController = true;

        return $this;
    } 

    public function addNamespaceInController($name): object
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

    protected function getController($name = null): string
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

    protected function getAction($name = null): string
    {
        $is      = (is_null($name));
        $default = $this->uri->getAction();
        $action  = (!$is) ? $name: $default;

        return $action . $this->actionSuffix;
    }

    protected function getParam(): array
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

    protected function getNameControllerError(): string
    {
        $i = 0;
        $error404 = null;
        $subDir   = '/';
        $ns       = '';

        if (!is_null($this->nameControllerError) ) {

            $error404       = $this->nameControllerError;
            $nameController = null;

            if(is_array($error404)) {

                $n = $this->controllerNamespace->get();
                $c = str_replace($n, '', self::getController() );
                $e = explode('\\', $c);
                $i = (count($e) > 1);

                if($i){
                    $subDir = $e[0];
                }

                if(in_array($subDir, array_keys($error404))){

                    $nameController = $error404[$subDir];
                }

            }

            $ns       = ($i)? $subDir . '\\': '';
            $error404 = $ns . $nameController;
        }

        return $error404;
    }

}    

