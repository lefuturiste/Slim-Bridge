<?php

namespace DI\Bridge\Slim;

class Bundle
{
	protected $app;
	protected $viewPath;

	public function __construct(App $app)
	{
		$this->app = $app;
	}

	public function getTwigPaths($twigPath)
	{
		return array_merge(
			$twigPath,
			[$this->getTwigPath()]
		);
	}

	public function getTwigPath(){
		return $this->viewPath;
	}
}
