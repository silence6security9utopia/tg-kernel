<?php


namespace Zcell\Kernel;


use Zcell\Kernel\MicroKernel\ApiMicroKernel;
use Zcell\Kernel\MicroKernel\BaseMicroKernel;
use Zcell\Kernel\MicroKernel\BotMicroKernel;
use Zcell\Kernel\MicroKernel\MicroKernelInterface;

class MicroKernelFactory
{
    /**
     * @var KernelInterface
     */
    protected KernelInterface $kernel;

    /**
     * MicroKernelFactory constructor.
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @param string $mode
     * @return MicroKernelInterface
     */
    public function make(string $mode): MicroKernelInterface
    {
        return $this->{$this->getMethodName($mode)}();
    }

    /**
     * @param string $mode
     * @return string
     */
    protected function getMethodName(string $mode): string
    {
        return 'create'.ucfirst($mode).'MicroKernel';
    }

    /**
     * @return BotMicroKernel
     */
    protected function createBotMicroKernel(): BaseMicroKernel
    {
        return new BotMicroKernel(
            $this->kernel->getApplication(),
            $this->kernel,
            $this->kernel->getApplication()->router,
            $this->kernel->getApplication()->eventDispatcher
        );
    }

    /**
     * @return BaseMicroKernel
     */
    protected function createApiMicroKernel(): BaseMicroKernel
    {
        return new ApiMicroKernel(
            $this->kernel->getApplication(),
            $this->kernel,
            $this->kernel->getApplication()->router,
            $this->kernel->getApplication()->eventDispatcher
        );
    }
}