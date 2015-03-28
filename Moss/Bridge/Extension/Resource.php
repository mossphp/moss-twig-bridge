<?php

/*
 * This file is part of the Moss Twig bridge package
 *
 * (c) Michal Wachowski <wachowski.michal@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Moss\Bridge\Extension;

use Moss\Bridge\TokenParser\Resource as TokenParserResource;

class Resource extends \Twig_Extension
{

    protected $forceCopy;
    protected $public;
    protected $bundle;
    protected $resources = [];

    public function __construct($forceCopy = false, $public = './resource/{bundle}/', $bundle = '../src/{bundle}/Resource/')
    {
        $this->forceCopy = (bool) $forceCopy;
        $this->public = $public;
        $this->bundle = $bundle;
    }

    /**
     * {@inheritdoc}
     */
    public function getTokenParsers()
    {
        return [new TokenParserResource()];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'resource';
    }

    /**
     * Builds resource copy or creates symlink to it
     *
     * @param $resource
     *
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     */
    public function build($resource)
    {
        $placeholders = $this->translate($resource);

        $public = strtr($this->public, $placeholders);
        $bundle = strtr($this->bundle, $placeholders);

        $this->buildDir($public);

        if ($this->forceCopy) {
            $this->buildCopy($public, $bundle);

            return $this->buildResourceName($public, $placeholders['{directory}'], $placeholders['{file}']);
        }

        try {
            $this->buildLink($public, $bundle);
        } catch (\BadFunctionCallException $e) {
            $this->buildCopy($public, $bundle);
        }

        $this->resources[] = $bundle;

        return $this->buildResourceName($public, $placeholders['{directory}'], $placeholders['{file}']);
    }

    /**
     * Splits identifier into parts
     *
     * @param string $identifier
     *
     * @return array
     * @throws \Twig_Error_Loader
     */
    protected function translate($identifier)
    {
        preg_match_all('/^(?P<bundle>[^:]+):(?P<directory>[^:]*:)?(?P<file>.+)$/', $identifier, $matches, \PREG_SET_ORDER);

        foreach (['bundle', 'file'] as $offset) {
            if (empty($matches[0][$offset])) {
                throw new \Twig_Error_Loader(sprintf('Invalid or missing "%s" node in resource filename "%s"', $offset, $identifier));
            }
        }

        $placeholders = [
            '{bundle}' => $matches[0]['bundle'],
            '{file}' => $matches[0]['file'],
            '{directory}' => isset($matches[0]['directory']) ? trim(str_replace(':', '\\', $matches[0]['directory']), '\\/') : null,
        ];

        return $placeholders;
    }

    /**
     * Builds resource name
     *
     * @param string $path
     * @param string $directory
     * @param string $file
     *
     * @return string
     */
    protected function buildResourceName($path, $directory, $file)
    {
        return rtrim($path, '/') . '/' . ($directory ? $directory . '/' : null) . $file;
    }

    /**
     * Builds recursive copy of resource directory or updates existing files.
     *
     * @param string $public
     * @param string $bundle
     *
     * @throws \Twig_Error_Runtime
     */
    protected function buildCopy($public, $bundle)
    {
        $iterator = new \RecursiveDirectoryIterator($bundle);
        $files = new \RecursiveIteratorIterator($iterator, \RecursiveIteratorIterator::SELF_FIRST);

        $length = strlen($bundle);

        /** @var $files \SplFileInfo[] */
        foreach ($files as $file) {
            if ($this->isDot($file)) {
                continue;
            }

            $target = $public . str_replace('\\', '/', substr($file->getPathname(), $length));

            if ($file->isDir()) {
                $this->buildDir($target);
                continue;
            }

            if (is_file($target) && $file->getMTime() <= filemtime($target)) {
                continue;
            }

            if (!copy($file->getPathname(), $target)) {
                throw new \Twig_Error_Runtime('Unable to copy resource file ' . $file->getPathname());
            }
        }
    }

    /**
     * Returns true if directory is dot
     *
     * @param \SplFileInfo $file
     *
     * @return bool
     */
    protected function isDot(\SplFileInfo $file)
    {
        return $file->getBasename() === '.' || $file->getBasename() === '..';
    }

    /**
     * Cuts filename from path
     *
     * @param string $path
     *
     * @return string
     */
    protected function cutFileName($path)
    {
        return rtrim(substr($path, 0, strrpos(rtrim($path, '/'), '/')), '/') . '/';
    }

    /**
     * Builds recursively directory structure matching passed path
     *
     * @param string $directory
     *
     * @throws \RuntimeException
     */
    protected function buildDir($directory)
    {
        if (is_dir($directory)) {
            return;
        }

        if (!mkdir($directory, 0777, true)) {
            throw new \RuntimeException(sprintf('Unable to create directory for resource %s', $directory));
        }
    }

    /**
     * Creates symlink to resource directory
     *
     * @param string $public
     * @param string $bundle
     *
     * @throws \Twig_Error_Runtime
     */
    protected function buildLink($public, $bundle)
    {
        if (file_exists($public)) {
            return;
        }

        if (!$path = realpath($bundle)) {
            throw new \Twig_Error_Runtime('Unable to resolve resource path to ' . $bundle);
        }

        if (!symlink($path, rtrim($public, '/'))) {
            throw new \Twig_Error_Runtime('Unable to create symlink for resource ' . $path . ' to ' . $public);
        }
    }
}
