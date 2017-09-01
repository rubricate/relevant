<?php

/*
 * @package     RubricatePHP
 * @author      Estefanio NS <estefanions AT gmail DOT com>
 * @link        http://rubricate.github.io
 * @copyright   2017 
 * 
 */


namespace Rubricate\Init;

class ControllerNamespaceInit implements IControllerNamespaceInit
{
    private $controllerNamespace;


    public function __construct($controllerNamespace)
    {
        self::setControllerNamespace($controllerNamespace);
    }


    public function get()
    {
        return $this->controllerNamespace;
    } 


    private function setControllerNamespace($controllerNamespace)
    {
        $ns = str_replace('.', '\\', $controllerNamespace);
        $this->controllerNamespace = rtrim($ns, '\\') . '\\' ;

        return $this;
    } 



}    


