<?php
namespace flowy;

use pocketmine\plugin\Plugin;

final class Flowy {
    const VERSION = '3.1.1';

    private function __construct() {}

    public static function run(Plugin $plugin, callable $callable, ...$arguments): Flow {
        return new Flow(new EventListener($plugin), $callable, $arguments);
    }
}