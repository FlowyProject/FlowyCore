<?php
namespace flowy;

use pocketmine\event\Event;

class Listen {
    /** @var string[] */
    protected $events;

    /** @var callable[] */
    protected $filters;

    public function __construct(string $event) {
        $this->events[] = $event;
        $this->filters = [];
    }

    public function getEvents(): array {
        return $this->events;
    }

    public function match(Event $event): bool {
        foreach($this->filters as $filter) {
            if(!$filter($event)) {
                return false;
            }
        }
        return true;
    }

    public function add(string $event): Listen {
        $this->events[] = $event;
        return $this;
    }

    public function filter(callable $predicate): Listen {
        $this->filters[] = $predicate;
        return $this;
    }
}

function listen(string $event): Listen {
    return new Listen($event);
}