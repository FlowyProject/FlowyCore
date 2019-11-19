<?php
namespace flowy;

use pocketmine\event\Event;

class Flow {
    /** @var \Closure */
    protected $flowDefinition;

    /** @var \Generator */
    protected $rawFlow;

    /** @var EventListener */
    protected $listener;

    /** @var bool */
    protected $running;

    /** @var string[] */
    protected $events;

    public function __construct(EventListener $listener, \Closure $flowDefinition) {
        $this->listener = $listener;
        $this->flowDefinition = $flowDefinition;
        $this->rawFlow = ($this->flowDefinition)();
        $this->listener->set_handler(\Closure::fromCallable(function($event){
            $this->continue($event);
        }));
        $this->running = false;
        $this->events = [];
    }

    public function continue(Event $event): void {
        if(!$this->valid()) throw new FlowyException();
        if($this->running) throw new FlowyException();
        if(!$this->rawFlow->current()->match($event)) return;

        $this->listener->cancelAll();

        $this->running = true;
        $ret = $this->rawFlow->send($event);
        $this->running = false;

        if(!$ret instanceof Listen) throw new FlowyException();
        foreach($ret->getEvents() as $class) {
            $this->listener->listen($class);
        }
    }

    public function kill(): void {
        if(!$this->valid()) return;
        if($this->running) throw new FlowyException();

        $this->flowDefinition = null;
        $this->rawFlow = null;
        $this->listener->dispose();
    }

    public function valid(): bool {
        return $this->rawFlow !== null && $this->rawFlow->valid();
    }

    public function is_running(): bool {
        return $this->running;
    }
}