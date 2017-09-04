<?php

abstract class BaseModule
{
    abstract public function entryPoint();

    public function __construct()
    {
        $this->entryPoint();
    }
}