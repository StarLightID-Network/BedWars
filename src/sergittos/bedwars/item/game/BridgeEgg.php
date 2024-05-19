<?php

declare(strict_types=1);


namespace sergittos\bedwars\item\game;


use pocketmine\entity\Location;
use pocketmine\entity\projectile\Throwable;
use pocketmine\item\Egg;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemTypeIds;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use sergittos\bedwars\game\entity\misc\BridgeEgg as BridgeEggEntity;

class BridgeEgg extends Egg {

    public function __construct() {
        parent::__construct(new ItemIdentifier(ItemTypeIds::EGG), "Bridge Egg");

        $this->setCustomName(TextFormat::RED . "Bridge Egg");
    }

    protected function createEntity(Location $location, Player $thrower): Throwable {
        return new BridgeEggEntity($location, $thrower);
    }
}