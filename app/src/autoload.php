<?php
spl_autoload_register(function($name){
    $class_name = __DIR__."/classes/$name.class.php";
    if(file_exists($class_name)) {
        require_once $class_name;
    }
});
spl_autoload_register(function($name){
    $class_name = __DIR__."/app/classes/$name.class.php";
    if(file_exists($class_name)) {
        require_once $class_name;
    }
});
spl_autoload_register(function($name){
    $class_name = __DIR__."/app/api/$name.php";
    if(file_exists($class_name)) {
        require_once $class_name;
    }
});
?>