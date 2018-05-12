<?php
namespace Flowy\Extension;

use Flowy\Flow\FlowInfo;
use Flowy\Flowy;

if(!interface_exists('Flowy\Extension\FlowyExtension')) {

    interface FlowyExtension
    {
        public function getName() : string;

        public function getVersion() : string;

        public function handleReturn(Flowy $flowy, FlowInfo $flowInfo) : bool;

        public function handleContinue(Flowy $flowy, FlowInfo $flowInfo) : bool;

        public function handleActiveChanged(Flowy $flowy, FlowInfo $flowInfo) : bool;

        public function handleDelete(Flowy $flowy, FlowInfo $flowInfo) : bool;
    }

}