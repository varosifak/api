<?php
class ReferenceLoader
{
    public $file;
    private $parsed;
    public function __construct($reference)
    {
        $this->file = $reference;
        $this->parsed = json_decode(file_get_contents(__DIR__."/../doc/json/".$reference));
    }
    public function get(){
        return $this->parsed;
    }
}