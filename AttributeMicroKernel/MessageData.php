<?php


namespace Zcell\Kernel\AttributeMicroKernel;

use Laravel\Lumen\Http\Request;

class MessageData
{
    protected $updateId;
    protected $chatId;
    protected $messageId;
    protected $fromId;
    protected $fromFirstName;
    protected $fromLastName;
    protected $chatFirstName;
    protected $chatLastName;
    protected $languageCode;
    protected $chatType;
    protected $date;
    protected $text;
    protected $method;
    protected $command;

    /**
     * MessageData constructor.
     * @param $updateId
     * @param $messageId
     * @param $from
     * @param $chat
     * @param $date
     * @param $text
     */
    public function __construct($updateId, $messageId, $from, $chat, $date, $text)
    {
        $this->updateId = $updateId;
        $this->messageId = $messageId;
        $this->fromId = $from['id'];
        $this->fromFirstName = $from['first_name'];
        $this->fromLastName = (isset($from['last_name']) === false)?null:$from['last_name'];
        $this->languageCode = (isset($from['language_code']) === false)?null:$from['language_code'];
        $this->chatId = $chat['id'];
        $this->chatFirstName = $chat['first_name'];
        $this->chatLastName = (isset($chat['last_name']) === false)?null:$chat['last_name'];
        $this->chatType = (isset($chat['type']) === false)?null:$chat['type'];
        $this->date = $date;
        $this->text = $text;
        $this->method = 'COMMAND';
        $this->command = $this->text;
    }

    /**
     * @param Request $request
     * @return MessageData
     */
    public static function createFromRequest(Request $request): MessageData
    {
        $data = $request->toArray();

        return new self(
            $data['update_id'],
            $data['message']['message_id'],
            $data['message']['from'],
            $data['message']['chat'],
            $data['message']['date'],
            $data['message']['text'],
        );
    }

    /**
     * @return string
     */
    public function getChatFirstName(): string
    {
        return $this->chatFirstName;
    }

    /**
     * @return int
     */
    public function getChatId(): int
    {
        return (int)$this->chatId;
    }

    /**
     * @return string
     */
    public function getChatLastName(): string
    {
        return $this->chatLastName;
    }

    /**
     * @return string
     */
    public function getChatType(): string
    {
        return $this->chatType;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return string
     */
    public function getFromFirstName(): string
    {
        return $this->fromFirstName;
    }

    /**
     * @return int
     */
    public function getFromId(): int
    {
        return (int)$this->fromId;
    }

    /**
     * @return string
     */
    public function getFromLastName(): string
    {
        return $this->fromLastName;
    }

    /**
     * @return mixed
     */
    public function getLanguageCode()
    {
        return $this->languageCode;
    }

    /**
     * @return int
     */
    public function getMessageId(): int
    {
        return (int)$this->messageId;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @return int
     */
    public function getUpdateId(): int
    {
        return (int)$this->updateId;
    }

    /**
     * @return string
     */
    public function getCommand(): string
    {
        return $this->command;
    }
}