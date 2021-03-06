<?php
namespace flowy;

use pocketmine\event\EventPriority;
use pocketmine\event\Listener;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\RegisteredListener;

if(!defined("flowy_EventListener")) {
    define("flowy_EventListener", 1);

    class EventListener implements Listener
    {

        /** @var Plugin */
        protected $owner;

        /** @var callable */
        protected $handler;

        /** @var RegisteredListener[] */
        protected $registeredListeners;

        /** @var bool */
        private $disposed;

        public function __construct(Plugin $plugin)
        {
            $this->owner = $plugin;
            $this->handler = null;
            $this->registeredListeners = [];
            $this->disposed = false;
        }

        public function set_handler(callable $handler): void
        {
            if ($this->disposed) return;

            $this->handler = $handler;
        }

        public function listen(string $event): void
        {
            if ($this->disposed) return;
            if ($this->handler === null) return;
            if (isset($this->registeredListeners[$event])) return;

            $this->registeredListeners[$event] = EventHelper::register(
                $event,
                $this,
                EventPriority::NORMAL,
                new CallbackEventExecutor($this->handler),
                $this->owner
            );
        }

        public function cancelAll(): void
        {
            foreach ($this->registeredListeners as $event => $listener) {
                EventHelper::unregister($event, $listener);
            }
            $this->registeredListeners = [];
        }

        public function dispose(): void
        {
            foreach ($this->registeredListeners as $event => $listener) {
                EventHelper::unregister($event, $listener);
            }
            $this->owner = null;
            $this->handler = null;
            $this->registeredListeners = [];
            $this->disposed = true;
        }
    }

}