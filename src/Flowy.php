<?php
namespace flowy;

use pocketmine\plugin\Plugin;

final class Flowy {
    const VERSION = '3.1.0';

    private function __construct() {}

    public static function run(Plugin $plugin, \Closure $closure, ...$arguments): Flow {
        return new Flow(new EventListener($plugin), $closure, $arguments);
    }
}