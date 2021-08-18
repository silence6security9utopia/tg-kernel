<?php


namespace Zcell\Kernel\AttributeMicroKernel;

use Vttrue\BotTemplate\Application;

abstract class FormEvent
{
    /**
     * @var Application
     */
    protected Application $app;

    /**
     * @var MessageData
     */
    protected MessageData $messageData;

    /**
     * @param MessageData $messageData
     */
    public function setMessageData(MessageData $messageData)
    {
        $this->messageData = $messageData;
    }

    /**
     * @return mixed
     */
    abstract public function handle();

}