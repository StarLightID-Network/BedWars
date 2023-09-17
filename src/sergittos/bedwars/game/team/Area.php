<?php
/*
* Copyright (C) Sergittos - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
*/

declare(strict_types=1);


namespace sergittos\bedwars\game\team;


use pocketmine\math\Vector3;
use function max;
use function min;

class Area {

    private Vector3 $first_vector;
    private Vector3 $second_vector;

    public function __construct(Vector3 $first_vector, Vector3 $second_vector) {
        $this->first_vector = $first_vector->floor();
        $this->second_vector = $second_vector->floor();
    }

    static public function fromData(array $data): Area {
        return new Area(
            new Vector3($data["first_x"], $data["first_y"], $data["first_z"]),
            new Vector3($data["second_x"], $data["second_y"], $data["second_z"])
        );
    }

    public function getMinX(): int {
        return min($this->first_vector->getFloorX(), $this->second_vector->getFloorX());
    }

    public function getMinY(): int {
        return min($this->first_vector->getFloorY(), $this->second_vector->getFloorY());
    }

    public function getMinZ(): int {
        return min($this->first_vector->getFloorZ(), $this->second_vector->getFloorZ());
    }

    public function getMaxX(): int {
        return max($this->first_vector->getFloorX(), $this->second_vector->getFloorX());
    }

    public function getMaxY(): int {
        return max($this->first_vector->getFloorY(), $this->second_vector->getFloorY());
    }

    public function getMaxZ(): int {
        return max($this->first_vector->getFloorZ(), $this->second_vector->getFloorZ());
    }

    public function isInside(Vector3 $position): bool {
        return $position->x >= $this->getMinX() && $position->x <= $this->getMaxX() and
            $position->z >= $this->getMinZ() && $position->z <= $this->getMaxZ();
    }

}