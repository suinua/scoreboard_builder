<?php


namespace scoreboard_builder\pmmp\service;


use pocketmine\network\mcpe\protocol\SetScorePacket;
use pocketmine\network\mcpe\protocol\types\ScorePacketEntry;
use pocketmine\Player;
use scoreboard_builder\ScoreboardSlot;

class DeleteScorePMMPService
{
    static function execute(Player $player, ScoreboardSlot $slot, int $scoreId): void {
        $entry = new ScorePacketEntry();
        $entry->objectiveName = $slot;
        $entry->scoreboardId = $scoreId;

        $pk = new SetScorePacket();
        $pk->type = $pk::TYPE_REMOVE;
        $pk->entries[] = $entry;
        $player->sendDataPacket($pk);
    }
}