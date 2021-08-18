<?php

use Zcell\Kernel\Common\MessageFactory;

if (!function_exists('message')) {
    function message(){
        return MessageFactory::getInstance();
    }
}