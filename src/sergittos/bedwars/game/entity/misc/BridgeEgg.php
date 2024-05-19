<?php

declare(strict_types=1);

namespace sergittos\bedwars\game\entity\misc;

use pocketmine\block\Air;
use pocketmine\block\Block;
use pocketmine\block\VanillaBlocks;
use pocketmine\block\Wool;
use pocketmine\entity\Entity;
use pocketmine\entity\Location;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\entity\projectile\Egg;
use pocketmine\event\entity\EntityCombustByEntityEvent;
use pocketmine\event\entity\EntityDamageByChildEntityEvent;
use pocketmine\math\RayTraceResult;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\player\Player;
use pocketmine\world\Position;
use sergittos\bedwars\game\Game;
use sergittos\bedwars\game\team\Team;
use sergittos\bedwars\session\SessionFactory;

class BridgeEgg extends Egg {

	private $startY;
	private $startVec;
	private $skippedFirst = false;

	private $isNotBE = false;

    public static function getNetworkTypeId(): string
    {
        return EntityIds::EGG;
    }

	public function __construct(Location $location, ?Entity $shootingEntity, ?CompoundTag $nbt = null){
		parent::__construct($location, $shootingEntity, $nbt);
		if($shootingEntity !== null){
			$this->setOwningEntity($shootingEntity);
            $session = SessionFactory::getSession($shootingEntity);

			if(!$shootingEntity instanceof Player){
				$this->isNotBE = true;
				return;
			}

			if(is_null($session->getGame())){
				$this->isNotBE = true;
			}
		}
	}

	protected function initEntity(CompoundTag $nbt) : void{
		parent::initEntity($nbt);
        $this->startVec = $this->getPosition()->asVector3();
		$this->startY = $this->getPosition()->getY();
	}

	protected function move(float $dx, float $dy, float $dz) : void{
		if($this->isNotBE){
			parent::move($dx, $dy, $dz);
			return;
		}
        if (!$this->getOwningEntity() instanceof Player) {
            return;
        }
        $session = SessionFactory::getSession($this->getOwningEntity());
		$game = $session->getGame();
		$team = $session->getTeam();
		if(!$team instanceof Team || !$game instanceof Game){
			parent::move($dx, $dy, $dz);
			return;
		}

		$world = $this->getWorld();
		$pos = $this->getPosition();
		$placePos = $pos->asVector3()->subtract(0, 1, 0);
		if(ceil($pos->getY() - $this->startY) > 2){
          $placePos = new Vector3($pos->getX(), $this->startY + 2, $pos->getZ());
		}else if(ceil($pos->getY() - $this->startY) < 2){
			$placePos = new Vector3($pos->getX(), $this->startY -2, $pos->getZ());
		}
		if($placePos->distance($this->startVec) > 50){
			$this->flagForDespawn();
            return;
		}

		parent::move($dx, $dy, $dz);
		if($this->skippedFirst){ //simple skip for players position
			foreach ([
				$placePos,
				$placePos->subtract(0, 0, 1),
				$placePos->subtract(1, 0, 0),
				$placePos->add(1, 0, 0),
				$placePos->add(0, 0, 1)
				] as $pos){
					if($world->getBlock($pos)->getTypeId() !== VanillaBlocks::BED()->getTypeId() && $world->getBlock($pos) instanceof Air){
						$world->setBlock($pos, VanillaBlocks::WOOL()->setColor($session->getTeam()->getDyeColor()));
                        $session->getGame()->addBlock(new Position($pos->getX(), $pos->getY(), $pos->getZ(), $world));
					}
				}
	    }
	    $this->skippedFirst = true;
	}


	protected function calculateInterceptWithBlock(Block $block, Vector3 $start, Vector3 $end) : ?RayTraceResult{
        if($block instanceof Wool && !$this->isNotBE){
        	return null;
        }
		return $block->calculateIntercept($start, $end);
	}

	/**
	 * Called when the projectile collides with an Entity.
	 */
	protected function onHitEntity(Entity $entityHit, RayTraceResult $hitResult) : void{
		if(!$this->isNotBE){
			return;
		}
		$damage = $this->getResultDamage();

		if($damage >= 0){
			if($this->getOwningEntity() === null){
				$ev = new EntityDamageByEntityEvent($this, $entityHit, EntityDamageEvent::CAUSE_PROJECTILE, $damage);
			}else{
				$ev = new EntityDamageByChildEntityEvent($this->getOwningEntity(), $this, $entityHit, EntityDamageEvent::CAUSE_PROJECTILE, $damage);
			}

			$entityHit->attack($ev);

			if($this->isOnFire()){
				$ev = new EntityCombustByEntityEvent($this, $entityHit, 5);
				$ev->call();
				if(!$ev->isCancelled()){
					$entityHit->setOnFire($ev->getDuration());
				}
			}
		}
		$this->flagForDespawn();
	}
}