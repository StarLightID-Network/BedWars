<?php

declare(strict_types=1);


namespace sergittos\bedwars\item\game;


use pocketmine\entity\Location;
use pocketmine\entity\projectile\Throwable;
use pocketmine\event\entity\ProjectileLaunchEvent;
use pocketmine\item\Egg;
use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemTypeIds;
use pocketmine\item\ItemUseResult;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use sergittos\bedwars\game\entity\misc\Bedug as BedugEntity;

class Bedug extends Item {

    public function __construct() {
        parent::__construct(new ItemIdentifier(ItemTypeIds::EGG), "Bridge Egg");

        $this->setCustomName(TextFormat::RED . "Bridge Egg");
    }

    public function onClickAir(Player $player, Vector3 $directionVector, array &$returnedItems) : ItemUseResult{
		$location = $player->getLocation();

        $projectile = 
		$projectile = $this->createEntity(Location::fromObject($player->getEyePos(), $player->getWorld(), $location->yaw, $location->pitch), $player);
		$projectile->setMotion($directionVector->multiply($this->getThrowForce()));

		$projectileEv = new ProjectileLaunchEvent($projectile);
		$projectileEv->call();
		if($projectileEv->isCancelled()){
			$projectile->flagForDespawn();
			return ItemUseResult::FAIL();
		}

		$projectile->spawnToAll();

		$location->getWorld()->addSound($location, new ThrowSound());

		$this->pop();

		return ItemUseResult::SUCCESS();
	}
}