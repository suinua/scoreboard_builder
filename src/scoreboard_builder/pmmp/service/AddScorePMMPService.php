<?php


namespace scoreboard_builder\pmmp\service;


use pocketmine\network\mcpe\protocol\SetScorePacket;
use pocketmine\network\mcpe\protocol\types\ScorePacketEntry;
use pocketmine\Player;
use scoreboard_builder\Score;
use scoreboard_builder\ScoreboardSlot;

class AddScorePMMPService
{
    static function execute(Player $player, ScoreboardSlot $slot, Score $score): void {
        $entry = new ScorePacketEntry();
        $entry->objectiveName = $slot->getText();
        $entry->type = $entry::TYPE_FAKE_PLAYER;
        $entry->customName = $score->getText();
        $entry->score = $score->getValue();
        $entry->scoreboardId = $score->getId();

        $pk = new SetScorePacket();
        $pk->type = $pk::TYPE_CHANGE;
        $pk->entries[] = $entry;
        $player->sendDataPacket($pk);
    }
}