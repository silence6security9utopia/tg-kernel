<?php


namespace Zcell\Kernel\Exceptions;


interface HttpExceptionInterface
{
    /**
     * @return int
     */
    public function getStatusCode(): int;
}