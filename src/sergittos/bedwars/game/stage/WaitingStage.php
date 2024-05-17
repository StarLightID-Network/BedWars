<?php

declare(strict_types=1);


namespace sergittos\bedwars\game\stage;


use sergittos\bedwars\game\stage\trait\JoinableTrait;
use sergittos\bedwars\session\Session;
use sergittos\bedwars\utils\GameUtils;

class WaitingStage extends Stage {
    use JoinableTrait {
        onJoin as onSessionJoin;
    }

    public function onJoin(Session $session): void {
        $this->onSessionJoin($session);
        $this->startIfReady();
    }

    private function startIfReady(): void {
        $map = $this->game->getMap();
        $count = $this->game->getPlayersCount();

        if($count > $map->getPlayersPerTeam()) {
            switch (GameUtils::getMode($map->getPlayersPerTeam())) {
                case "Solo":
                    if ($count >= ($map->getMaxCapacity() / 4)) {
                        $this->game->setStage(new StartingStage());
                    }
                    break;
                case "Duos":
                    if ($count >= ($map->getMaxCapacity() / 4)) {
                        $this->game->setStage(new StartingStage());
                    }
                    break;
                case "Squads":
                    if ($count >= ($map->getMaxCapacity() / 2)) {
                        $this->game->setStage(new StartingStage());
                    }
                    break;
            }
        }
    }

    public function tick(): void {}

}