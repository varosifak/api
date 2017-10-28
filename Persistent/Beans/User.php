<?php

class User extends BeansBase
{
    function __construct($condition=null)
    {
        parent::__construct($condition);
        $this->addUniques(array('neptun'));
    }
}
