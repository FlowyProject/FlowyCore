<?php
namespace flowy;

class Listen {
    /** @var string[] */
    protected $events;

    public function __construct(string $event) {
        $this->events[] = $event;
    }

    public function getEvents(): array {
        return $this->events;
    }

    public function add(string $event): void {
        $this->events[] = $event;
    }
}

function listen(string $event): Listen {
    return new Listen($event);
}