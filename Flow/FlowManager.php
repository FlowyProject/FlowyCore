<?php
namespace Flowy\Flow;

if(!class_exists('Flowy\Flow\FlowManager')) {

    class FlowManager implements FlowManagerInterface
    {
        /** @var FlowInfo[] */
        private $flowInfoMap = [];

        private $nextId = 0;

        private $addHandler;
        private $deleteHandler;
        private $activeChangedHandler;
        private $continueHandler;

        public function __construct()
        {
            $this->setAddHandler([ $this, 'defaultAddHandler' ]);
            $this->setDeleteHandler([ $this, 'defaultDeleteHandler' ]);
            $this->setDefaultActiveChangedHandler([ $this, 'defaultActiveChangedHandler' ]);
            $this->setDefaultContinueHandler([ $this, 'defaultContinueHandler' ]);
        }

        public function add(\Generator $flow, bool $active = true) : FlowInfo
        {
            if (($this->addHandler)($flow, $active)) {
                return $this->flowInfoMap[$this->nextId] = new FlowInfo($this, $flow, $this->nextId++, $active, $this->activeChangedHandler, $this->continueHandler);
            }
            return null;
        }

        public function delete(int $flowId) : void
        {
            if (($this->deleteHandler)($this->get($flowId))) {
                $this->flowInfoMap[$flowId]->setActive(false);
                unset($this->flowInfoMap[$flowId]);
            }
        }

        public function get(int $flowId) : FlowInfo
        {
            if (!isset($this->flowInfoMap[$flowId])) {
                throw new \OutOfBoundsException();
            }
            return $this->flowInfoMap[$flowId];
        }

        public function exists(FlowInfo $flowInfo) : bool
        {
            return isset($this->flowInfoMap[$flowInfo->getFlowId()]) && $this->flowInfoMap[$flowInfo->getFlowId()]->getFlow() === $flowInfo->getFlow();
        }

        public function existsId(int $flowId) : bool
        {
            return isset($this->flowInfoMap[$flowId]);
        }

        public function getIterator() : \ArrayIterator
        {
            return new \ArrayIterator($this->flowInfoMap);
        }

        /** @see defaultAddHandler */
        public function setAddHandler(?callable $handler) : void
        {
            $this->addHandler = $handler ?? [ $this, 'defaultAddHandler' ];
        }

        /** @see defaultDeleteHandler */
        public function setDeleteHandler(?callable $handler) : void
        {
            $this->deleteHandler = $handler ?? [ $this, 'defaultDeleteHandler' ];
        }

        /** @see defaultActiveChangedHandler */
        public function setDefaultActiveChangedHandler(?callable $handler) : void
        {
            $this->activeChangedHandler = $handler ?? [ $this, 'defaultActiveChangedHandler' ];
        }

        /** @see defaultContinueHandler */
        public function setDefaultContinueHandler(?callable $handler) : void
        {
            $this->continueHandler = $handler ?? [ $this, 'defaultContinueHandler' ];
        }

        public function defaultAddHandler(\Generator $flow, bool $active) : bool
        {
            return true; // Always add
        }

        public function defaultDeleteHandler(FlowInfo $flowInfo) : bool
        {
            return true; // Always delete
        }

        public function defaultActiveChangedHandler(FlowInfo $flowInfo) : void
        {
            // Nothing to do
        }

        public function defaultContinueHandler(FlowInfo $flowInfo) : void
        {
            // Nothing to do
        }
    }

}