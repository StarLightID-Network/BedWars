<?php

declare(strict_types=1);


namespace sergittos\bedwars\game\stage;


use pocketmine\world\sound\ClickSound;
use sergittos\bedwars\game\stage\trait\JoinableTrait;
use sergittos\bedwars\session\Session;
use sergittos\bedwars\utils\GameUtils;
use function count;
use function str_replace;

class StartingStage extends Stage {
    use JoinableTrait {
        onQuit as onSessionQuit;
    }

    private int $countdown = 10;

    public function getCountdown(): int {
        return $this->countdown;
    }

    public function onQuit(Session $session): void {
        $this->onSessionQuit($session);
        $map = $this->game->getMap();
        $count = count($this->game->getPlayers());
        switch (GameUtils::getMode($map->getPlayersPerTeam())) {
            case "Solo":
                if ($count < ($map->getMaxCapacity() / 4)) {
                    $this->game->setStage(new WaitingStage());
                }
                break;
            case "Duos":
                if ($count < ($map->getMaxCapacity() / 4)) {
                    $this->game->setStage(new WaitingStage());
                }
                break;
            case "Squads":
                if ($count < ($map->getMaxCapacity() / 2)) {
                    $this->game->setStage(new WaitingStage());
                }
                break;
        }
    }

    public function tick(): void {
        if($this->countdown <= 0) {
            $this->game->setStage(new PlayingStage());
        } elseif($this->countdown <= 5) {
            $this->broadcastCountdownTitle();
        }
        if($this->countdown > 0) {
            $this->game->broadcastMessage($this->getStartingMessage());
        }
        if($this->countdown === 5) {
            $this->game->setupWorld();
        } elseif($this->countdown === 10) {
            $this->broadcastCountdownTitle();
        }
        $this->game->updateScoreboards();

        $this->countdown--;
    }

    private function getStartingMessage(): string {
        $message = "{YELLOW}The game is starting within {time} {YELLOW}seconds!";
        if($this->countdown <= 10) {
            $message = "{YELLOW}The game starts in {time} {YELLOW}" . ($this->countdown === 1 ? "second" : "seconds") . "!";
        }
        $message = str_replace("{time}", GameUtils::getColoredMessageNumber($this->countdown), $message);

        return $message;
    }

    private function broadcastCountdownTitle(): void {
        $this->game->broadcastTitle(GameUtils::getColoredTitleNumber($this->countdown));
        $this->game->broadcastSound(new ClickSound());
    }

}