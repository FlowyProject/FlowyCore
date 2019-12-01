<?php
namespace flowy;

use pocketmine\event\Event;
use pocketmine\event\Listener;
use pocketmine\plugin\EventExecutor;

class ClosureEventExecutor implements EventExecutor {
    /** @var \Closure */
    private $closure;

    public function __construct(\Closure $closure) {
        $this->closure = $closure;
    }

    public function execute(Listener $listener, Event $event) {
        ($this->closure)($event);
    }

    public function getClosure(): \Closure {
        return $this->closure;
    }
}