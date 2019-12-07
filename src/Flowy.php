<?php
namespace flowy;

use pocketmine\plugin\Plugin;

if(!defined("flowy_Flowy")) {
    define("flowy_Flowy", 1);

    final class Flowy
    {
        const VERSION = '3.1.2.1';

        private function __construct()
        {
        }

        public static function run(Plugin $plugin, callable $callable, ...$arguments): Flow
        {
            return new Flow(new EventListener($plugin), $callable, $arguments);
        }
    }

}