<?php


namespace Zcell\Kernel\AttributeMicroKernel;


class MessageResponse implements BotResponseInterface
{
    /**
     * @var null|string|int
     */
    protected ?int $chat_id = null;
    /**
     * @var string
     */
    protected ?string $text = null;

    /**
     * @var null
     */
    protected ?string $reply_markup = null;

    /**
     * MessageResponse constructor.
     * @param $chat_id
     * @param string|null $text
     */
    public function __construct($chat_id, ?string $text = null)
    {
        $this->chat_id = $chat_id;
        $this->text = $text;
    }

    /**
     * @param string $text
     * @return MessageResponse
     */
    public function setText(string $text): MessageResponse
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @param array $keyboards
     * @return MessageResponse
     */
    public function setInlineKeyboard(array $keyboards): MessageResponse
    {
        $this->reply_markup = json_encode($keyboards);

        return $this;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        $base = [];

        foreach ($this->getFields() as $field) {
            $value = $this->{$field};

            if ($value !== null) {
                $base[$field] = $value;
            }
        }

        return json_encode($base);
    }

    /**
     * @return string[]
     */
    protected function getFields(): array
    {
        return [
            'chat_id',
            'text',
            'reply_markup'
        ];
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return 'sendMessage';
    }
}