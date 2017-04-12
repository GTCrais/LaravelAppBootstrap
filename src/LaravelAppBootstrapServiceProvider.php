<?php

namespace GTCrais\LaravelAppBootstrap;

use Illuminate\Support\ServiceProvider;

class LaravelAppBootstrapServiceProvider extends ServiceProvider
{
	protected $commands = [
		'GTCrais\LaravelAppBootstrap\Console\Commands\Setup'
	];

	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->publishes([
			__DIR__ . '/files/config' => config_path(),
			__DIR__ . '/files/config.localdev' => base_path('/config.localdev'),
			__DIR__ . '/files/bootstrap' => base_path('/bootstrap'),
		]);
	}

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register()
	{
		if ($this->app->environment() != 'production') {
			$this->app->register('Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider');
		}
		$this->app->register('PhanAn\CascadingConfig\CascadingConfigServiceProvider');

		$this->commands($this->commands);
	}
}
