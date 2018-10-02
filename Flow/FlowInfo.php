<?php
namespace Flowy\Flow;

if(!class_exists('Flowy\Flow\FlowInfo')) {

    class FlowInfo
    {

        /** @var FlowManager */
        private $manager;

        /** @var \Generator */
        private $flow;

        /** @var int */
        private $flowId;

        /** @var bool */
        private $active;

        /** @var callable */
        private $activeChangedHandler;

        /** @var callable */
        private $continueHandler;

        public function __construct(FlowManager $manager, \Generator $flow, int $flowId, bool $active, callable $activeChangedHandler, callable $continueHandler)
        {
            $this->manager = $manager;
            $this->flow = $flow;
            $this->flowId = $flowId;
            $this->active = $active;
            $this->activeChangedHandler = $activeChangedHandler;
            $this->continueHandler = $continueHandler;
        }

        public function getManager() : FlowManager
        {
            return $this->manager;
        }

        public function getFlow() : \Generator
        {
            return $this->flow;
        }

        public function getFlowId() : int
        {
            return $this->flowId;
        }

        public function isActive() : bool
        {
            return $this->active;
        }

        public function getActiveChangedHandler() : callable
        {
            return $this->activeChangedHandler;
        }

        public function setActive(bool $active) : void
        {
            $change = $this->active !== $active;
            $this->active = $active;
            if ($change && $this->activeChangedHandler !== null) {
                ($this->activeChangedHandler)($this);
            }
        }

        public function getReturn()
        {
            return $this->flow->valid() ?
                ($this->flow->current()) :
                ($this->flow->getReturn() ?? $this->flow->current());
        }

        public function delete() : void
        {
            $this->manager->delete($this->flowId);
            $this->manager = null;
        }

        public function continue($arg = null) : bool
        {
            if (!$this->active) {
                return false;
            }
            if ($this->continueHandler !== null) {
                ($this->continueHandler)($this);
            }
            $this->flow->send($arg);
            if (!$this->flow->valid() && $this->getReturn() === null) {
                $this->delete();
                return false;
            }
            return true;
        }
    }

}