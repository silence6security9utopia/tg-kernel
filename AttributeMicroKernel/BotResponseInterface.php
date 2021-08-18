<?php


namespace Zcell\Kernel\AttributeMicroKernel;


interface BotResponseInterface
{
    /**
     * @return string
     */
    public function getMethod(): string;

    /**
     * @return string
     */
    public function getMessage(): string;
}