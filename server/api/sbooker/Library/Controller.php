<?php

abstract class Controller 
{ 
    /*
        function of forming  view, if client requests data if format html
        /if required to expand the project/
        @param <String> $view - name view
        @param <Array> data for view
    */ 
    public  function render($view, array $args = [])
    { 
        extract($args);
        $dir = get_class($this);
        $file =  "View/".$dir ."/". $view;
        if(!file_exists($file))
        {
           throw new \Exception("{$file} not found!".PHP_EOL.__FILE__.PHP_EOL."- in ".__LINE__.PHP_EOL);
        }
        
        require_once $file;
    }
}
