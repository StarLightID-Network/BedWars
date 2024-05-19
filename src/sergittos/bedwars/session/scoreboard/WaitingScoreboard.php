<?php

declare(strict_types=1);


namespace sergittos\bedwars\session\scoreboard;


use sergittos\bedwars\game\Game;
use sergittos\bedwars\game\stage\StartingStage;
use sergittos\bedwars\session\Session;
use sergittos\bedwars\utils\ConfigGetter;
use sergittos\bedwars\utils\GameUtils;
use function strtolower;

class WaitingScoreboard extends Scoreboard {

    protected function getLines(Session $session): array {
        $game = $session->getGame();
        $map = $game->getMap();
        $stage = $game->getStage();
        return [
            10 => " ",
            9 => "{YELLOW}Map: {GREEN}" . $map->getName(),
            8 => "{YELLOW}Players: {GREEN}" . $game->getPlayersCount() . "/" . $map->getMaxCapacity(),
            7 => "  ",
            6 => !$stage instanceof StartingStage ? "{WHITE}Waiting..." : "{WHITE}Starting in {GREEN}" . $stage->getCountdown() . "s",
            5 => "   ",
            4 => "{YELLOW}Mode: {GREEN}" . GameUtils::getMode($map->getPlayersPerTeam()),
        ];
    }
}