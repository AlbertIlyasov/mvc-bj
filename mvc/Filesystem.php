<?php

namespace MVC;

class Filesystem
{
    public function getFiles(string $path): array
    {
        if (!in_array(substr($path, -1), ['\\', '/'])) {
            $path .= '/';
        }
        $pathItems = null;
        try {
            $pathItems = scandir($path);
        } catch (\Exception $e){}
        if (!$pathItems) {
            return [];
        }

        $files = [];
        foreach ($pathItems as $itemName) {
            $pathItem = $path . $itemName;
            if (is_file($pathItem)) {
                $files[] = $pathItem;
            } elseif (!in_array($itemName, ['.', '..']) && is_dir($pathItem)) {
                $files = array_merge($files, $this->getFiles($pathItem));
            }
        }
        return $files;
    }
}
