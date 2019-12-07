<?php
namespace flowy;

use pocketmine\event\Event;
use pocketmine\event\Listener;
use pocketmine\plugin\EventExecutor;

class CallbackEventExecutor implements EventExecutor {
    /** @var callable */
    private $callback;

    public function __construct(callable $callback) {
        $this->callback = $callback;
    }

    public function execute(Listener $listener, Event $event) {
        ($this->callback)($event);
    }

    public function getCallback(): callable {
        return $this->callback;
    }
}