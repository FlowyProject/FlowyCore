<?php
namespace Flowy\Flow;

if(!class_exists('Flowy\Flow\FlowInfo')) {

    class FlowInfo
    {

        /** @var FlowRepository */
        private $repository;

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

        public function __construct(FlowRepository $repository, \Generator $flow, int $flowId, bool $active, callable $activeChangedHandler, callable $continueHandler)
        {
            $this->repository = $repository;
            $this->flow = $flow;
            $this->flowId = $flowId;
            $this->active = $active;
            $this->activeChangedHandler = $activeChangedHandler;
            $this->continueHandler = $continueHandler;
        }

        public function getRepository() : FlowRepository
        {
            return $this->repository;
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

        public function setActive(bool $active)
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
            $this->repository->delete($this->flowId);
            $this->repository = null;
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