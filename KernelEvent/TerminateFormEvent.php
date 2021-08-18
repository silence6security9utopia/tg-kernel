<?php


namespace Zcell\Kernel\KernelEvent;


use Laravel\Lumen\Http\Request;
use Zcell\Kernel\AttributeMicroKernel\MessageData;
use Zcell\Kernel\Kernel;

class TerminateFormEvent extends BaseEvent
{
    /**
     * @var MessageData
     */
    protected MessageData $messageData;

    /**
     * TerminateFormEvent constructor.
     * @param Kernel $kernel
     * @param Request $request
     * @param MessageData $messageData
     */
    public function __construct(Kernel $kernel, Request $request, MessageData $messageData)
    {
        parent::__construct($kernel, $request);

        $this->messageData = $messageData;
    }

    /**
     * @return MessageData
     */
    public function getMessageData(): MessageData
    {
        return $this->messageData;
    }
}