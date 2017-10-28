<?php

use \MicroLight\Components\JSON as JSON;

abstract class BeansBase extends \RedBean_SimpleModel
{
    protected $id;
    protected $attributes;
    protected $classname;
    protected $node;
    private $uniqueInformations = array();

    public function __construct($condition)
    {
        $classInfo = explode('\\', get_class($this));
        $this->classname = strtolower(array_pop($classInfo)) . 's';
        if ($condition) {
            $this->node = R::findOne($this->classname, ' ' . $condition . ' ');
            if ($this->node) {
                $this->id = $this->node->id;
                $userDataSet = array(
                    'code' => 200,
                    'message' => 'User has been found with ' . $condition . ' conditions.',
                    'id' => $this->id
                );
                $this->node->setMeta("buildcommand.unique", $this->uniqueInformations);
            } else {
                $userDataSet = array(
                    'code' => 404,
                    'message' => 'User not found with ' . $condition . ' conditions.'
                );
            }
        } else {
            $this->node = R::dispense($this->classname);
            $userDataSet = array(
                'code' => 201,
                'message' => 'New user object has been created.'
            );
            $this->node->setMeta("buildcommand.unique", $this->uniqueInformations);
        }
        JSON::set('user', $userDataSet);
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
}