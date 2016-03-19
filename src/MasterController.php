<?php

namespace App;

use Pimple\Container;

class MasterController {
    
    private $routes;

    /** @var  Container */
    private $container;

    public function __construct(array $routes, Container $container) {
        $this->routes = $routes;
        $this->container = $container;
    }
    
    public function execute() {
        $call = $this->_determineControllers();
        $call_class = $call['call'];
        $class = ucfirst(array_shift($call_class));
        $method = array_shift($call_class);

        $o = $this->container[$class];
        return $o->$method();
    }
    
    private function _determineControllers()
    {
        if (isset($_SERVER['REDIRECT_BASE'])) {
            $rb = $_SERVER['REDIRECT_BASE'];
        } else {
            $rb = '';
        }
        
        $ruri = $_SERVER['REQUEST_URI'];
        $path = str_replace($rb, '', $ruri);
        $return = array();

        foreach($this->routes as $k => $v) {
            $matches = array();
            $pattern = '$' . $k . '$';
            if(preg_match($pattern, $path, $matches))
            {
                $controller_details = $v;
                $controller_method = explode('@', $controller_details);
                $return = array('call' => $controller_method);
            }
        }
        
        return $return;
    }
}
