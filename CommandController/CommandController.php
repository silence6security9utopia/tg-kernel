<?php


namespace Zcell\Kernel\CommandController;

use Illuminate\Container\Container;
use Zcell\Kernel\AttributeMicroKernel\MessageData;

abstract class CommandController
{
    /**
     * @var Container
     */
    protected Container $app;

    /**
     * @var MessageData
     */
    protected MessageData $messageData;

    /**
     * CommandController constructor.
     * @param Container $app
     * @param MessageData $messageData
     */
    public function __construct(Container $app, MessageData $messageData)
    {
        $this->messageData = $messageData;
        $this->app = $app;
    }

    /**
     * @param string $formEventClass
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function insertFormEvent(string $formEventClass): void
    {
        $redisClient = $this->app->make('redis');

        $configuration = $this->getConfiguration()['hash_table'];

        $redisClient->hSet($configuration['form_event'], $this->messageData->getChatId(), $formEventClass);
    }

    /**
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    private function getConfiguration()
    {
        return $this->app->make('config')->get('storage');
    }

}