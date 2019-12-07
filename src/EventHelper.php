<?php
namespace flowy;

use pocketmine\event\Event;
use pocketmine\event\HandlerList;
use pocketmine\event\Listener;
use pocketmine\plugin\EventExecutor;
use pocketmine\plugin\MethodEventExecutor;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginException;
use pocketmine\plugin\RegisteredListener;
use pocketmine\Server;
use pocketmine\timings\TimingsHandler;
use pocketmine\utils\Utils;

/**
 * Referenced
 * https://github.com/pmmp/PocketMine-MP/blob/25ff90b2c6880f1179210b8da073d89aadc44d5e/src/pocketmine/plugin/PluginManager.php
 */

if(!defined("flowy_EventHelper")) {
    define("flowy_EventHelper", 1);

    final class EventHelper
    {
        private function __construct()
        {
        }

        public static function register(string $event, Listener $listener, int $priority, EventExecutor $executor, Plugin $plugin, bool $ignoreCancelled = false): RegisteredListener
        {
            if (!is_subclass_of($event, Event::class)) {
                throw new PluginException("{$event} is not an Event");
            }

            $server = Server::getInstance();

            $tags = Utils::parseDocComment((string)(new \ReflectionClass($event))->getDocComment());
            if (isset($tags["deprecated"]) and $server->getProperty("settings.deprecated-verbose", true)) {
                $server->getLogger()->warning($server->getLanguage()->translateString("pocketmine.plugin.deprecatedEvent", [
                    $plugin->getName(),
                    $event,
                    get_class($listener) . "->" . ($executor instanceof MethodEventExecutor ? $executor->getMethod() : "<unknown>")
                ]));
            }

            if (!$plugin->isEnabled()) {
                throw new PluginException("Plugin attempted to register {$event} while not enabled");
            }

            $timings = new TimingsHandler("Plugin: " . $plugin->getDescription()->getFullName() . " Event: " . get_class($listener) . "::" . ($executor instanceof MethodEventExecutor ? $executor->getMethod() : "???") . "(" . (new \ReflectionClass($event))->getShortName() . ")");
            $registeredListener = new RegisteredListener($listener, $executor, $priority, $plugin, $ignoreCancelled, $timings);
            self::getEventListeners($event)->register($registeredListener);
            return $registeredListener;
        }

        public static function unregister(string $event, RegisteredListener $listener): void
        {
            self::getEventListeners($event)->unregister($listener);
        }

        private static function getEventListeners(string $event): HandlerList
        {
            $list = HandlerList::getHandlerListFor($event);
            if ($list === null) {
                throw new PluginException("Abstract events nont declaring @allowHandle cannot be handled (tried to register listener for {$event})");
            }
            return $list;
        }
    }

}