<?php


namespace scoreboard_builder;


use pocketmine\Player;
use pocketmine\utils\TextFormat;
use scoreboard_builder\pmmp\service\AddScorePMMPService;
use scoreboard_builder\pmmp\service\DeleteScorePMMPService;
use scoreboard_builder\pmmp\service\DeleteScoreboardPMMPService;
use scoreboard_builder\pmmp\service\SendScoreboardPMMPService;

class Scoreboard
{
    /**
     * @var ScoreboardSlot
     */
    static protected $slot;
    /**
     * @var string
     */
    static protected $title;
    /**
     * @var ScoreSortType
     */
    static protected $sortType;
    /**
     * @var Score[]
     */
    static protected $scores;

    //setting
    static private $autoIndex;

    private function __construct(ScoreboardSlot $slot, string $title, array $scores, ScoreSortType $sortType, bool $autoIndex = true) {
        self::$slot = $slot;
        self::$title = $title;
        self::$sortType = $sortType;
        self::$scores = $scores;
        self::$autoIndex = $autoIndex;
    }

    protected static function __create(ScoreboardSlot $slot, string $title, array $scores, ScoreSortType $sortType, bool $autoIndex = true): Scoreboard {
        return new Scoreboard($slot, $title, $scores, $sortType, $autoIndex);
    }

    protected static function __send(Player $player, Scoreboard $scoreboard): void {
        SendScoreboardPMMPService::execute($player, $scoreboard);

        if (self::$autoIndex) {
            //指定済みのValueを取り出す
            $usedValues = [];
            foreach (self::$scores as $score) {
                if ($score->getValue() !== null) $usedValues[] = $score->getValue();
            }

            $index = 0;
            foreach ($scoreboard->getScores() as $score) {
                //Valueの指定されているものはそのまま
                if ($score->getValue() !== null) {
                    self::addScore($player, $score);
                    continue;
                }

                //指定されてないValueを0から順番に探していく
                while (in_array($index, $usedValues)) {
                    $index++;
                }

                $usedValues[] = $index;
                self::addScore($player, new Score($score->getText(), $index, $index));
            }
        } else {
            foreach ($scoreboard->getScores() as $score) {
                self::addScore($player, $score);
            }
        }
    }

    protected static function __update(Player $player, Scoreboard $scoreboard): void {
        self::delete($player);
        self::__send($player, $scoreboard);
    }

    protected static function delete(Player $player): void {
        DeleteScoreboardPMMPService::execute($player, self::$slot);
    }

    public static function addScore(Player $player, Score $score): void {
        self::$scores[] = $score;

        //$textが重複していたら、TextFormat::RESETを重複している分だけリピートする
        //Scoreboard::$scoresには変化を与えない
        if (self::hasSameTextScore($score->getText())) {
            $text = $score->getText() . str_repeat(TextFormat::RESET, self::countSameTextScore($score->getText()));
            new Score($text, $score->getValue(), $score->getId());
        }
        AddScorePMMPService::execute($player, self::$slot, $score);
    }

    public static function deleteScore(Player $player, Score $targetScore): void {
        foreach (self::$scores as $index => $score) {
            if ($score->getId() === $targetScore) {
                unset(self::$scores[$index]);
            }
        }

        DeleteScorePMMPService::execute($player, self::$slot, $targetScore->getId());
    }

    static function updateScore(Player $player, Score $score) {
        self::deleteScore($player, $score);
        self::addScore($player, $score);
    }

    /**
     * @return ScoreboardSlot
     */
    public static function getSlot(): ScoreboardSlot {
        return self::$slot;
    }

    /**
     * @return string
     */
    public static function getTitle(): string {
        return self::$title;
    }

    /**
     * @return ScoreSortType
     */
    public static function getSortType(): ScoreSortType {
        return self::$sortType;
    }

    /**
     * @return Score[]
     */
    public static function getScores(): array {
        return self::$scores;
    }

    /**
     * @return bool
     */
    public static function getAutoIndex(): bool {
        return self::$autoIndex;
    }


    private static function hasSameTextScore(string $text): bool {
        foreach (self::$scores as $score) {
            if ($score->getText() === $text) {
                return true;
            }
        }

        return false;
    }

    private static function countSameTextScore(string $text): int {

        $count = 0;

        foreach (self::$scores as $score) {
            if ($score->getText() === $text) {
                $count++;
            }
        }

        return $count;
    }
}