<?php
namespace flowy;

use pocketmine\event\EventPriority;
use pocketmine\event\Listener;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\RegisteredListener;

class EventListener implements Listener {

    /** @var Plugin */
    protected $owner;

    /** @var \Closure */
    protected $handler;

    /** @var RegisteredListener[] */
    protected $registeredListeners;

    /** @var bool */
    private $disposed;

    public function __construct(Plugin $plugin) {
        $this->owner = $plugin;
        $this->handler = null;
        $this->registeredListeners = [];
        $this->disposed = false;
    }

    public function set_handler(\Closure $handler): void {
        if($this->disposed) return;

        $this->handler = $handler;
    }

    public function listen(string $event): void {
        if($this->disposed) return;
        if($this->handler === null) return;
        if(isset($this->registeredListeners[$event])) return;

        $this->registeredListeners[$event] = EventHelper::register(
            $event,
            $this,
            EventPriority::NORMAL,
            new ClosureEventExecutor($this->handler),
            $this->owner
        );
    }

    public function cancelAll(): void {
        foreach($this->registeredListeners as $event => $listener) {
            EventHelper::unregister($event, $listener);
        }
        $this->registeredListeners = [];
    }

    public function dispose(): void {
        foreach($this->registeredListeners as $event => $listener) {
            EventHelper::unregister($event, $listener);
        }
        $this->owner = null;
        $this->handler = null;
        $this->registeredListeners = [];
        $this->disposed = true;
    }
}