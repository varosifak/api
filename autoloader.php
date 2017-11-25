<?php
spl_autoload_register(function ($class) {
    @include_once(__DIR__."/".str_replace('\\', '/', $class).".php");
    @include_once(__DIR__."/MicroLight/Components/".$class.".php");
    @include_once(__DIR__."/MicroLight/Model/".$class.".php");
    @include_once(__DIR__."/modules/".$class.".php");
});