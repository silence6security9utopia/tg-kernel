<?php


namespace Zcell\Kernel\KernelListener;


use Illuminate\Http\JsonResponse;
use Psr\Log\LoggerInterface;
use Zcell\Kernel\AttributeMicroKernel\MessageResponse;
use Zcell\Kernel\Kernel;
use Zcell\Kernel\KernelEvent\ErrorEvent;

class ErrorListener
{
    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logging;

    /**
     * ErrorListener constructor.
     * @param LoggerInterface $logging
     */
    public function __construct(LoggerInterface $logging)
    {
        $this->logging = $logging;
    }

    /**
     * @param ErrorEvent $event
     */
    public function setBotResponse(ErrorEvent $event): void
    {
        if ($event->getKernel()->getCurrentMode() === Kernel::COMMAND_MODE) {
            $event->setResponse(new MessageResponse($event->getKernel()->getChatId(), $event->getMessage()));
        }
    }

    /**
     * @param ErrorEvent $event
     */
    public function setApiResponse(ErrorEvent $event): void
    {
        if ($event->getKernel()->getCurrentMode() === Kernel::API_MODE) {
            $event->setResponse(new JsonResponse(['message' => $event->getMessage()], $event->getStatus()));
        }
    }

    /**
     * @param ErrorEvent $event
     */
    public function setDefaultResponse(ErrorEvent $event): void
    {
        if ($event->getKernel()->getCurrentMode() === null) {
            $event->setResponse(new JsonResponse(['message' => $event->getMessage()], $event->getStatus()));
        }
    }

    /**
     * @param ErrorEvent $event
     */
    public function logSystemError(ErrorEvent $event): void
    {
        if ($event->getStatus() >= 500) {
            $this->logging->error($event->getMessage(), ['exception' => $event->getError()]);
        }
    }
}