<?php
namespace Harmony2\Http;

interface Response {
    public function send($return = false);
}