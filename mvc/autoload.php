<?php

namespace MVC;

class Autoload
{
    private $filesystem;

    public function __construct()
    {
        require_once 'Filesystem.php';
        $this->filesystem = new Filesystem;

        $components = array_merge(
            $this->getCoreComponents(),
            $this->getCustomComponents()
        );

        foreach ($components as $component) {
            require_once $component;
        }  
    }

    private function getCoreComponents()
    {
        return array_unique(array_merge(
            $this->filesystem->getFiles(__DIR__ . '/traits'),
            [
                'requestInterface.php',
            ],
            $this->filesystem->getFiles(__DIR__)
        ));
    }

    private function getCustomComponents()
    {
        return array_merge(
            $this->filesystem->getFiles(__DIR__ . '/../controllers'),
            $this->filesystem->getFiles(__DIR__ . '/../models')
        );
    }
}
