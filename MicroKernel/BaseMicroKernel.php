<?php


namespace Zcell\Kernel\MicroKernel;


use Vttrue\BotTemplate\Router;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Vttrue\BotTemplate\Application;
use Zcell\Kernel\KernelInterface as Kernel;

abstract class BaseMicroKernel implements MicroKernelInterface
{
    /**
     * @var Application
     */
    protected Application $app;

    /**
     * @var Kernel
     */
    protected Kernel $kernel;

    /**
     * @var Router
     */
    protected Router $router;

    /**
     * @var EventDispatcher
     */
    protected EventDispatcher $dispatcher;

    /**
     * BaseMicroKernel constructor.
     * @param Application $app
     * @param Kernel $kernel
     * @param Router $router
     * @param EventDispatcher $dispatcher
     */
    public function __construct(Application $app, Kernel $kernel, Router $router, EventDispatcher $dispatcher)
    {
        $this->app = $app;
        $this->kernel = $kernel;
        $this->router = $router;
        $this->dispatcher = $dispatcher;
    }
}