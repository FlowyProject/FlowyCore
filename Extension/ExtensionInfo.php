<?php
namespace Flowy\Extension;

use Flowy\Flowy;
use Flowy\FlowyException;

if(!class_exists('Flowy\Extension\ExtensionInfo')) {

    class ExtensionInfo
    {
        static public function load(string $extension)
        {
            return new ExtensionInfo($extension);
        }

        private function __construct(string $extension)
        {
            if (!class_exists($extension))
                throw new FlowyException();
            if (!is_subclass_of($extension, FlowyExtension::class))
                throw new FlowyException();

            $this->instance = new $extension();
            $this->loadMethods();
        }

        private $instance;

        public function getInstance() : FlowyExtension
        {
            return $this->instance;
        }

        public function getClass() : string
        {
            return get_class($this->instance);
        }

        public function getName() : string
        {
            return $this->getInstance()->getName();
        }

        public function getVersion() : string
        {
            return $this->getInstance()->getVersion();
        }

        private $methods = [];

        private function loadMethods()
        {
            $reflection = new \ReflectionClass($this->getClass());
            foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                if ($method->isStatic()) continue;

                $params = $method->getParameters();
                if (1 <= count($params) && substr($method->getName(), -8) === 'ExMethod' && $params[0]->getClass() instanceof \ReflectionClass && is_a($params[0]->getClass()->getName(), Flowy::class, true)) {
                    $this->methods[] = $method->getName();
                }
            }
        }

        public function getMethods()
        {
            return $this->methods;
        }
    }

}