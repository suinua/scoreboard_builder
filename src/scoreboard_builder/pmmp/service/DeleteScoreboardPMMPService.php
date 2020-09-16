<?php

namespace scoreboard_builder\pmmp\service;


use pocketmine\network\mcpe\protocol\RemoveObjectivePacket;
use pocketmine\Player;
use scoreboard_builder\ScoreboardSlot;

class DeleteScoreboardPMMPService
{
    static function execute(Player $player, ScoreboardSlot $slot):void{
        $pk = new RemoveObjectivePacket();
        $pk->objectiveName = $slot->getText();
        $player->sendDataPacket($pk);
    }
}