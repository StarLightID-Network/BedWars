<?php

declare(strict_types=1);


namespace sergittos\bedwars\listener;


use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\player\Player;
use sergittos\bedwars\session\SessionFactory;

class WaitingListener implements Listener {

    public function onBreak(BlockBreakEvent $event): void {
        if($this->checkWorld($event->getPlayer())) {
            $event->cancel();
        }
    }

    public function onPlace(BlockPlaceEvent $event): void {
        if($this->checkWorld($event->getPlayer())) {
            $event->cancel();
        }
    }

    public function onInteract(PlayerInteractEvent $event): void {
        if($this->checkWorld($event->getPlayer())) {
            $event->cancel();
        }
    }

    public function onReceiveDamage(EntityDamageEvent $event): void {
        $entity = $event->getEntity();
        $cause = $event->getCause();
        if($entity instanceof Player and $this->checkWorld($entity)) {
            if ($cause === EntityDamageEvent::CAUSE_VOID) {
                $entity->teleport($entity->getWorld()->getSafeSpawn());
                $event->cancel();
            }
            $event->cancel();
        }
    }

    private function checkWorld(Player $player): bool {
        $session = SessionFactory::getSession($player);
        return $session !== null and $session->isPlaying() and $session->getGame()->getMap()->getWaitingWorld() === $player->getWorld();
    }
}