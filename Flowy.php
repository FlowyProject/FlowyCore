<?php
namespace Flowy;

use Flowy\Extension\ExtensionManagerTrait;
use Flowy\Flow\FlowInfo;
use Flowy\Flow\FlowManager;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\plugin\PluginDescription;
use pocketmine\plugin\PluginLoader;
use pocketmine\Server;

if(!class_exists('Flowy\Flowy')) {

    abstract class Flowy implements Listener
    {
        use ExtensionManagerTrait;

        const VERSION = '3.0.0';

        /** @var FlowManager */
        protected $flowManager;

        public function getFlowManager()
        {
            return $this->flowManager;
        }

        public function __call($name, $arguments)
        {
            if (!isset($this->extensionMethods[$name]))
                throw new FlowyException("Call to undefined function ${name}");
            ($this->extensions[$this->extensionMethods[$name]]->getInstance())->$name($this, ...$arguments);
        }

        public function __construct(PluginLoader $loader, Server $server, PluginDescription $description, string $dataFolder, string $file)
        {
            $this->initExtension();

            $this->flowManager = new FlowManager();
            $activeChangedHandler = function (FlowInfo $flowInfo) : void {
                foreach ($this->extensions as $extension) {
                    if ($extension->getInstance()->handleActiveChanged($this, $flowInfo)) {
                        break;
                    }
                }
            };
            $continueHandler = function (FlowInfo $flowInfo) : void {
                foreach ($this->extensions as $extension) {
                    if ($extension->getInstance()->handleContinue($this, $flowInfo)) {
                        break;
                    }
                }
            };
            $deleteHandler = function (FlowInfo $flowInfo) : bool {
                foreach ($this->extensions as $extension) {
                    if ($extension->getInstance()->handleDelete($this, $flowInfo)) {
                        break;
                    }
                }
                return true;
            };
            $this->flowManager->setDefaultActiveChangedHandler($activeChangedHandler);
            $this->flowManager->setDefaultContinueHandler($continueHandler);
            $this->flowManager->setDeleteHandler($deleteHandler);

            parent::__construct($loader, $server, $description, $dataFolder, $file);
        }

        public function manage(\Generator $flow, bool $active = true) : FlowInfo
        {
            $flowInfo = $this->flowManager->add($flow, $active);
            $this->handleReturn($flowInfo);
            return $flowInfo;
        }

        public function handleReturn(FlowInfo $flowInfo) : void
        {
            if (!$this->flowManager->exists($flowInfo))
                throw new FlowyException("An unknown FlowInfo was passed");

            foreach ($this->extensions as $extension) {
                if ($extension->getInstance()->handleReturn($this, $flowInfo)) {
                    break;
                }
            }
        }
    }

}