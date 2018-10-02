<?php
namespace Flowy\Flow;

if(!interface_exists('Flowy\Flow\FlowManagerInterface')) {

    interface FlowManagerInterface
    {
        public function add(\Generator $flow, bool $active = true) : FlowInfo;

        public function delete(int $flowId) : void;

        public function get(int $flowId) : FlowInfo;

        public function exists(FlowInfo $flowInfo) : bool;

        public function existsId(int $flowId) : bool;

        public function getIterator() : \ArrayIterator;

        /** @see defaultAddHandler */
        public function setAddHandler(callable $handler) : void;

        /** @see defaultDeleteHandler */
        public function setDeleteHandler(callable $handler) : void;

        /** @see defaultActiveChangedHandler */
        public function setDefaultActiveChangedHandler(callable $handler) : void;

        /** @see defaultContinueHandler */
        public function setDefaultContinueHandler(callable $handler) : void;

        public function defaultAddHandler(\Generator $flow, bool $active) : bool;

        public function defaultDeleteHandler(FlowInfo $flowInfo) : bool;

        public function defaultActiveChangedHandler(FlowInfo $flowInfo) : void;

        public function defaultContinueHandler(FlowInfo $flowInfo) : void;
    }

}