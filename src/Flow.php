<?php
namespace flowy;

use pocketmine\event\Event;

if(!defined("flowy_Flow")) {
    define("flowy_Flow", 1);

    class Flow
    {
        /** @var callable */
        protected $flowDefinition;

        /** @var array */
        protected $flowArguments;

        /** @var \Generator */
        protected $rawFlow;

        /** @var EventListener */
        protected $listener;

        /** @var bool */
        protected $running;

        /** @var string[] */
        protected $events;

        public function __construct(EventListener $listener, callable $flowDefinition, array $flowArguments = [])
        {
            $this->listener = $listener;
            $this->flowDefinition = $flowDefinition;
            $this->flowArguments = $flowArguments;
            $this->rawFlow = ($this->flowDefinition)(...$this->flowArguments);
            $this->listener->set_handler([$this, 'continue']);
            $this->running = false;
            $this->events = [];
            $this->listenAll();
        }

        public function continue(Event $event): void
        {
            if (!$this->valid()) throw new FlowyException();
            if ($this->running) throw new FlowyException();
            if (!$this->rawFlow->current()->match($event)) return;

            $this->listener->cancelAll();
            $this->run($event);
            if ($this->valid()) {
                $this->listenAll();
            } else {
                $this->forceShutdown();
            }
        }

        protected function run(Event $event): void
        {
            $this->running = true;
            $ret = $this->rawFlow->send($event);
            $this->running = false;
        }

        protected function listenAll(): void
        {
            $ret = $this->rawFlow->current();
            if (!$ret instanceof Listen) throw new FlowyException();
            foreach ($ret->getEvents() as $class) {
                $this->listener->listen($class);
            }
        }

        public function kill(): void
        {
            if (!$this->valid()) return;
            if ($this->running) throw new FlowyException();

            $this->forceShutdown();
        }

        protected function forceShutdown(): void
        {
            $this->flowDefinition = null;
            $this->rawFlow = null;
            $this->listener->dispose();
        }

        public function valid(): bool
        {
            return $this->rawFlow !== null && $this->rawFlow->valid();
        }

        public function is_running(): bool
        {
            return $this->running;
        }
    }

}