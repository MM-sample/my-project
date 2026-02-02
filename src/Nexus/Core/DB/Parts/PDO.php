<?php

namespace Nexus\Core\DB\Parts;

final class PDO extends \PDO {

    public function __clone():void {
        throw new \ErrorException('Clone is not allowed against '. get_class($this));
    }

}
