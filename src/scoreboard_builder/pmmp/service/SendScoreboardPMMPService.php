<?php

namespace scoreboard_builder\pmmp\service;

use pocketmine\network\mcpe\protocol\SetDisplayObjectivePacket;
use pocketmine\Player;
use scoreboard_builder\Scoreboard;

class SendScoreboardPMMPService
{
    static function execute(Player $player, Scoreboard $scoreboard): void {
        $pk = new SetDisplayObjectivePacket();
        $pk->displaySlot = $scoreboard->getSlot()->getText();
        $pk->objectiveName = $scoreboard->getSlot()->getText();
        $pk->displayName = $scoreboard->getTitle();
        $pk->criteriaName = "dummy";
        $pk->sortOrder = $scoreboard->getSortType()->getValue();
        $player->sendDataPacket($pk);
    }
}