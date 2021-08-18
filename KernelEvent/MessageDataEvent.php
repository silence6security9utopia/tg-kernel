<?php


namespace Zcell\Kernel\KernelEvent;


use Laravel\Lumen\Http\Request;
use Zcell\Kernel\AttributeMicroKernel\FormEvent;
use Zcell\Kernel\AttributeMicroKernel\MessageData;
use Zcell\Kernel\Kernel;

class MessageDataEvent extends BaseEvent
{
    /**
     * @var MessageData
     */
    protected MessageData $messageData;

    /**
     * @var string|null
     */
    protected ?string $formEvent = null;

    /**
     * MessageDataEvent constructor.
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
     * @param string $formEvent
     */
    public function setFormEvent(string $formEvent)
    {
        $this->formEvent = $formEvent;
    }

    /**
     * @return FormEvent|null
     */
    public function getFormEvent(): ?string
    {
        return $this->formEvent;
    }

    /**
     * @return bool
     */
    public function isFormEvent(): bool
    {
        return ! is_null($this->formEvent);
    }

    /**
     * @return MessageData
     */
    public function getMessageData(): MessageData
    {
        return $this->messageData;
    }
}