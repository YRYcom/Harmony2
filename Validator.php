<?php

namespace Harmony2;


class Validator
{

  private $fails = [];


  public function __construct() {

  }

  public function fails() {
    return (count($this->fails)>0);
  }

  public function getFails() {
    return $this->fails;
  }

  public function setFail($key) {
    $this->fails[$key] = true;
  }
}