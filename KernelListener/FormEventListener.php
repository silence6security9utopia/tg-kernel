<?php


namespace Zcell\Kernel\KernelListener;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Redis\RedisManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Zcell\Kernel\KernelEvent\MessageDataEvent;
use Zcell\Kernel\KernelEvent\TerminateFormEvent;

class FormEventListener implements EventSubscriberInterface
{
    /**
     * @var RedisManager
     */
    protected RedisManager $redisManager;

    /**
     * @var Repository
     */
    protected Repository $config;

    /**
     * @var array|null
     */
    protected ?array $hashTable;

    /**
     * FormEventListener constructor.
     * @param RedisManager $redisManager
     * @param Repository $config
     */
    public function __construct(RedisManager $redisManager, Repository $config)
    {
        $this->redisManager = $redisManager;
        $this->config = $config;
    }

    /**
     * @param MessageDataEvent $event
     */
    public function setFormEvent(MessageDataEvent $event): void
    {
        $messageData = $event->getMessageData();

        if ($messageData->getText() !== null) {
            $formEvent = $this->selectFormEvent($messageData->getChatId());

            if (! is_null($formEvent)) {
                $event->setFormEvent($formEvent);
            }
        }
    }

    /**
     * @param $userId
     * @return null
     */
    protected function selectFormEvent($userId)
    {
        $configuration = $this->getConfiguration();

        $result = $this->redisManager->hGet($configuration['form_event'], $userId);

        if (! $result) {
            return null;
        }

        return $result;
    }

    /**
     * @param $userId
     */
    protected function deleteFormEvent($userId)
    {
        $configuration = $this->getConfiguration();

        $this->redisManager->hDel($configuration['form_event'], $userId);
    }

    /**
     * @param TerminateFormEvent $event
     */
    public function terminateFormEvent(TerminateFormEvent $event): void
    {
        $messageData = $event->getMessageData();

        $this->deleteFormEvent($messageData->getChatId());
    }

    /**
     * @return null|array
     */
    protected function getConfiguration(): ?array
    {
        return $this->hashTable ?? $this->hashTable = $this->config->get('storage.hash_table');
    }


    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            //
        ];
    }
}