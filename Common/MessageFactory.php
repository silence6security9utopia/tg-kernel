<?php


namespace Zcell\Kernel\Common;


use Zcell\Kernel\AttributeMicroKernel\MessageData;
use Zcell\Kernel\AttributeMicroKernel\MessageResponse;
use Zcell\Kernel\AttributeMicroKernel\PoolMessage;

class MessageFactory
{
    /**
     * @var MessageData
     */
    protected MessageData $messageData;

    /**
     * @var self|null
     */
    private static ?self $instance = null;

    /**
     * MessageFactory constructor.
     * @param MessageData $messageData
     */
    public function __construct(MessageData $messageData)
    {
        $this->messageData = $messageData;
    }

    /**
     * @param string|null $text
     * @return MessageResponse
     */
    public function message(string $text = null): MessageResponse
    {
        return new MessageResponse($this->messageData->getChatId(), $text);
    }

    /**
     * @return PoolMessage
     */
    public function pool(): PoolMessage
    {
        return new PoolMessage();
    }

    /**
     * @return static
     */
    public static function getInstance(): self
    {
        if (self::$instance instanceof self) {
            return self::$instance;
        }

        return self::$instance = new self(
            app()->make(MessageData::class)
        );
    }
}