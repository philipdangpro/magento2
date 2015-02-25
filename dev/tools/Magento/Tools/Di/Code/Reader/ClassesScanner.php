<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Tools\Di\Code\Reader;

use Magento\Framework\Filesystem\FilesystemException;
use Zend\Code\Scanner\FileScanner;

class ClassesScanner
{
    /**
     * @var array
     */
    protected $excludePatterns = [];

    /**
     * adds exclude patterns
     *
     * @param array $excludePatterns
     */
    public function addExcludePatterns(array $excludePatterns)
    {
        $this->excludePatterns = array_merge($this->excludePatterns, $excludePatterns);
    }
    
    /**
     * Retrieves list of classes for given path
     *
     * @param string $path
     *
     * @return array
     *
     * @throws FilesystemException
     */
    public function getList($path)
    {
        $realPath = realpath($path);
        if (!(bool)$realPath) {
            throw new FilesystemException();
        }

        $recursiveIterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($realPath, \FilesystemIterator::FOLLOW_SYMLINKS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        $classes = [];
        foreach ($recursiveIterator as $fileItem) {
            /** @var $fileItem \SplFileInfo */
            if ($fileItem->getExtension() !== 'php') {
                continue;
            }
            foreach ($this->excludePatterns as $excludePattern) {
                if (preg_match($excludePattern, $fileItem->getRealPath())) {
                    continue 2;
                }
            }
            $fileScanner = new FileScanner($fileItem->getRealPath());
            $classNames = $fileScanner->getClassNames();
            foreach ($classNames as $className) {
                if (!class_exists($className)) {
                    require_once $fileItem->getRealPath();
                }
                $classes[] = $className;
            }
        }
        return $classes;
    }
}
