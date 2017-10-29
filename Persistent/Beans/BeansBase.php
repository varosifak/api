<?php

use \MicroLight\Components\JSON as JSON;

abstract class BeansBase extends \RedBean_SimpleModel
{
    public static $version = "1.0.0";
    protected $id;
    protected $attributes;
    protected $classname;
    protected $node;
    private $uniqueInformations = array();

    public function __construct($condition)
    {
        $classInfo = explode('\\', get_class($this));
        $this->classname = strtolower(array_pop($classInfo)) . 's';
    }

    public
    function addUniques($array)
    {
        array_push($this->uniqueInformations, $array);
    }

    public
    function __set($name, $value): void
    {
        if (method_exists($this, $name)) {
            $this->$name($value);
        } else {
            $this->attributes[$name] = $value;
        }
    }

    public
    function __get($name)
    {
        print $name;
        if (method_exists($this, $name)) {
            return $this->$name();
        } elseif (isset($this->attributes[$name])) {
            return $this->attributes[$name];
        }
        return null;
    }

    private
    function copyAttributes($node): RedBeanPHP\OODBBean
    {
        if ($this->attributes) {
            foreach ($this->attributes as $key => $value) {
                $node->$key = $value;
            }
        }
        return $node;
    }

    public
    function save(): int
    {
        if (!$this->id) {
            $this->node = $this->copyAttributes($this->node);
        }
        return R::store($this->node);
    }
    public function delete(){
        R::trash( $this->node );
    }
    public function refresh(){
        $this->node = $this->node->fresh();
    }
    public static function wipe($bean){
        R::wipe($bean);
    }
}