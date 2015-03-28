<?php

/*
 * This file is part of the Moss Twig bridge package
 *
 * (c) Michal Wachowski <wachowski.michal@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Moss\Bridge\Loader;

class File implements \Twig_LoaderInterface
{

    protected $pattern;

    public function __construct($pattern = '../src/{bundle}/{directory}/View/{file}.twig')
    {
        $this->pattern = $pattern;
    }

    /**
     * {@inheritdoc}
     */
    public function getSource($name)
    {
        return file_get_contents($this->translate($name));
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheKey($name)
    {
        return $this->translate($name);
    }

    /**
     * {@inheritdoc}
     */
    public function isFresh($name, $time)
    {
        $file = $this->translate($name);

        return filemtime($file) < $time;
    }

    /**
     * Translates identifier to file
     *
     * @param $identifier
     *
     * @return mixed|string
     * @throws \Twig_Error_Loader
     */
    protected function translate($identifier)
    {
        preg_match_all('/^(?P<bundle>[^:]+):(?P<directory>[^:]*:)?(?P<file>.+)$/', $identifier, $matches, \PREG_SET_ORDER);

        $replacements = array();
        foreach (array('bundle', 'directory', 'file') as $key) {
            if (empty($matches[0][$key])) {
                throw new \Twig_Error_Loader(sprintf('Invalid or missing "%s" node in view filename "%s"', $key, $identifier));
            }

            $replacements['{' . $key . '}'] = str_replace(':', '\\', $matches[0][$key]);
        }

        $file = strtr($this->pattern, $replacements);
        $file = str_replace(array('\\', '_', '//'), '/', $file);

        if (!is_file($file)) {
            throw new \Twig_Error_Loader(sprintf('Unable to load template file %s (%s)', $identifier, $file));
        }

        return $file;
    }
}
