<?php
namespace Flowy\Extension;

use Flowy\FlowyException;

if(!trait_exists('Flowy\Extension\ExtensionManagerTrait')) {

    trait ExtensionManagerTrait
    {
        static private $extensionClassList = [];

        static public function install(string $extension) : void
        {
            if (!class_exists($extension))
                throw new FlowyException("Class not defined");
            if (!is_subclass_of($extension, FlowyExtension::class))
                throw new FlowyException("Extension must inherit FlowyExtension interface");
            if (in_array($extension, self::$extensionClassList, true))
                throw new FlowyException("${extension} has already installed");

            self::$extensionClassList[] = $extension;
        }

        static public function uninstall(string $extension) : void
        {
            if (($index = array_search($extension, self::$extensionClassList, true)) !== false) {
                unset(self::$extensionClassList[$index]);
            }
        }

        static public function isInstalled(string $extension) : bool
        {
            return in_array($extension, self::$extensionClassList);
        }

        private $extensionMethods;

        /** @var ExtensionInfo[] */
        private $extensions;

        private function initExtension() : void
        {
            $this->extensions = [];
            $this->extensionMethods = [];
            foreach (self::$extensionClassList as $class) {
                $extension = ExtensionInfo::load($class);
                $name = $extension->getName();
                $this->extensions[$name] = $extension;
                foreach ($extension->getMethods() as $methodName) {
                    if (isset($this->extensionMethods[$methodName]))
                        throw new FlowyException("${methodName} method of ${name} has already defined");
                    $this->extensionMethods[$methodName] = $name;
                }
            }
        }
    }

}