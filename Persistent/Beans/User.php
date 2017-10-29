<?php
use \MicroLight\Components\JSON as JSON;
class User extends BeansBase
{
    public static $version = "0.0.1";
    function __construct($condition=null)
    {
        parent::__construct($condition);
        $this->addUniques(array('neptun'));
        if ($condition) {
            $this->node = R::findOne($this->classname, ' ' . $condition . ' ');
            if ($this->node) {
                $this->id = $this->node->id;
                $userDataSet = array(
                    'code' => 200,
                    'message' => 'User has been found with ' . $condition . ' conditions.',
                    'id' => $this->id
                );
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
        }
        JSON::set($this->classname, $userDataSet);
    }
}
