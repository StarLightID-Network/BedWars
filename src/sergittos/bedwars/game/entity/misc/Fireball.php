<?php

declare(strict_types=1);


namespace sergittos\bedwars\game\entity\misc;


use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\projectile\Throwable;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\world\Explosion;
use pocketmine\world\Position;

class Fireball extends Throwable {

    protected $life = 0;

    public static function getNetworkTypeId(): string {
        return EntityIds::FIREBALL;
    }

    protected function getInitialSizeInfo(): EntitySizeInfo {
        return new EntitySizeInfo(0.50, 0.50);
    }

    protected function getName(): string
    {
        return "FireBall";
    }

    protected function getInitialGravity(): float {
        return 0.0;
    }

    protected function entityBaseTick(int $tickDiff = 1): bool
    {
        if ($this->closed) {
            return false;
        }
        if ($this->getOwningEntity() === null) {
            $this->flagForDespawn();
            return false;
        }
        $this->life++;
        $this->updateMovement();
        if ($this->life > 200) {
            $this->flagForDespawn();
        }
        return true;
    }

    protected function onHit(ProjectileHitEvent $event): void {
        $explosion = new Explosion(Position::fromObject($event->getRayTraceResult()->getHitVector(), $this->getWorld()), 4, $this);
        $explosion->explodeA();
        $explosion->explodeB();
    }

    public function attack(EntityDamageEvent $source) : void{
		if($source->getCause() === EntityDamageEvent::CAUSE_VOID){
			parent::attack($source);
		}
		if($source instanceof EntityDamageByEntityEvent){
		     $damager = $source->getDamager();
			 $this->setMotion($damager->getDirectionVector()->add(0, 0, 0)->multiply(0.5));
		}
	}
}