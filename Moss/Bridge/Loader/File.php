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

class File implements \Twig_LoaderInterface {

	protected $pattern;

	public function __construct($pattern = '../src/{bundle}/{directory}/View/{file}.twig') {
		$this->pattern = $pattern;
	}

	public function getSource($name) {
		return file_get_contents($this->traslate($name));
	}

	public function getCacheKey($name) {
		return $this->traslate($name);
	}

	public function isFresh($name, $time) {
		$file = $this->traslate($name);
		return filemtime($file) < $time;
	}

	protected function traslate($name) {
		preg_match_all('/^(?P<bundle>[^:]+):(?P<directory>[^:]*:)?(?P<file>.+)$/', $name, $matches, \PREG_SET_ORDER);

		$r = array();
		foreach(array('bundle', 'directory', 'file') as $k) {
			if(empty($matches[0][$k])) {
				throw new \Twig_Error_Loader(sprintf('Invalid or missing "%s" node in view filename "%s"', $k, $name));
			}

			$r['{' . $k .'}'] = str_replace(':', '\\', $matches[0][$k]);
		}

		$file = strtr($this->pattern, $r);
		$file = str_replace(array('\\', '_', '//'), '/', $file);

		if(!is_file($file)) {
			throw new \Twig_Error_Loader(sprintf('Unable to load template file %s (%s)', $name, $file));
		}

		return $file;
	}
}
