<?php

/*
 * @package     RubricatePHP
 * @author      Estefanio NS <estefanions AT gmail DOT com>
 * @link        http://rubricate.github.io
 * @copyright   2017 - 2018
 * 
 */


namespace Rubricate\Relevant;

class ControllerNamespaceRelevant implements IControllerNamespaceRelevant
{
    private $controllerNamespace;


    public function __construct($controllerNamespace)
    {
        self::init($controllerNamespace);
    }


    public function get()
    {
        return $this->controllerNamespace;
    } 


    private function init($controllerNamespace)
    {
        $search = array('.', '-', '_');
        $ns = str_replace($search, '\\', $controllerNamespace);
        $this->controllerNamespace = rtrim($ns, '\\') . '\\' ;

        return $this;
    } 



}    

