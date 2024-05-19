<?php

declare(strict_types=1);


namespace sergittos\bedwars\game\stage;


use sergittos\bedwars\session\Session;
use function count;

class EndingStage extends Stage {

    private int $time = 5;

    private bool $tie = false;

    protected function onStart(): void {
        foreach($this->game->getSpectators() as $session) {
            $session->title("{RED}GAME OVER!");
        }
        $this->tie = count($this->game->getAliveTeams()) > 1;
    }

    public function onJoin(Session $session): void {
        if(!$this->tie) {
            $session->addWin();
            $session->addCoins(140);
        }

        $session->title($this->getTitle(), $this->getSubTitle(), 0, $this->time * 20);
        $session->resetSettings();
    }

    public function onQuit(Session $session): void {
        $this->game->despawnGeneratorsFrom($session);
    }

    public function tick(): void {
        $this->time--;
        if($this->time <= 0) {
            $this->game->reset();
        }
    }

    private function getTitle(): string {
        return $this->tie ? "{ORANGE}TIE!" : "{BOLD}{GOLD}VICTORY!";
    }

    private function getSubTitle(): string {
        return $this->tie ? "{GRAY}You were 2 last man standing" : "{GRAY}You were the last man standing";
    }
}