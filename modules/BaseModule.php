<?php
abstract class BaseModule
{
    abstract static public function any();
    abstract static public function get();
    abstract static public function post();
    abstract static public function delete();
    abstract static public function put();
}