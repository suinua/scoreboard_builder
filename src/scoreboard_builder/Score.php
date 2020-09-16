<?php


namespace scoreboard_builder;


use pocketmine\utils\UUID;

class Score
{
    private $text;
    private $value;
    private $id;


    /**
     * Score constructor.
     * @param string $text
     * @param int|null $value
     * @param int|null $id
     *
     * 自動でインデックスさせる場合は$valueを指定しないでください
     */
    public function __construct(string $text, ?int $value = null, ?int $id = null) {
        $this->text = $text;
        $this->value = $value;
        try {
            $this->id = $id ?? random_int(0, PHP_INT_MAX);
        } catch (\Exception $exception) {
            $this->id = 0;
        }
    }


    /**
     * @return string
     */
    public function getText(): string {
        return $this->text;
    }

    /**
     * @return null|int
     */
    public function getValue(): ?int {
        return $this->value;
    }

    /**
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }
}