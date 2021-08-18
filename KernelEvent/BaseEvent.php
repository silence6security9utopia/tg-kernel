<?php


namespace Zcell\Kernel\KernelEvent;

use Laravel\Lumen\Http\Request;
use Symfony\Contracts\EventDispatcher\Event;
use Zcell\Kernel\Kernel;

class BaseEvent extends Event
{
    /**
     * @var Kernel
     */
    protected Kernel $kernel;

    /**
     * @var Request
     */
    protected Request $request;

    /**
     * BaseEvent constructor.
     * @param Kernel $kernel
     * @param Request $request
     */
    public function __construct(Kernel $kernel, Request $request)
    {
        $this->kernel = $kernel;
        $this->request = $request;
    }

    /**
     * @return Kernel
     */
    public function getKernel(): Kernel
    {
        return $this->kernel;
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }
}