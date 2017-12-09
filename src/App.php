<?php

namespace DI\Bridge\Slim;

use DI\ContainerBuilder;

/**
 * Slim application configured with PHP-DI.
 *
 * As you can see, this class is very basic and is only useful to get started quickly.
 * You can also very well *not* use it and build the container manually.
 */
class App extends \Slim\App
{
	protected $bundles = [];
	protected $hasBundles = false;

	public function __construct()
	{
		//on initialise le container
		$containerBuilder = new ContainerBuilder;
		$containerBuilder->addDefinitions(__DIR__ . '/config.php');

		if ($this->hasBundles) {
			//pour chaque bundle, on l'initialise
			foreach ($this->getBundles() as $bundle) {
				$bundle = new $bundle($this);
				array_push($this->bundles, $bundle);
				//on configure les container pour ce bundle
				$bundle->configureContainer($containerBuilder);
				//on ajoute le path twig à ceux existant
				$this->twigPaths = $bundle->getTwigPaths($this->twigPaths, $containerBuilder);
			}
		}

		//on configure les container de l'app principale
		$this->configureContainer($containerBuilder);
		//on met les twig path dans les container
		$this->configureTwigPathArray($containerBuilder);
		$container = $containerBuilder->build();

		parent::__construct($container);

		if ($this->hasBundles) {
			// on enregistre les routes des bundles
			$this->registerBundlesRoutes();
		}
	}

	/**
	 * Override this method to configure the container builder.
	 *
	 * For example, to load additional configuration files:
	 *
	 *     protected function configureContainer(ContainerBuilder $builder)
	 *     {
	 *         $builder->addDefinitions(__DIR__ . 'my-config-file.php');
	 *     }
	 */
	protected function configureContainer(ContainerBuilder $builder)
	{
	}

	public function getBundles()
	{
		return [];
	}
	
	protected function configureTwigPathArray(ContainerBuilder $containerBuilder)
	{
		$containerBuilder->addDefinitions([
			'twig_paths' => $this->twigPaths
		]);
	}

	protected function registerBundlesRoutes()
	{
		foreach ($this->bundles as $bundle) {
			$bundle->routes();
		}
	}
}
