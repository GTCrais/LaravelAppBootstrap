<?php

namespace GTCrais\LaravelAppBootstrap;

use Illuminate\Support\ServiceProvider;

class LaravelAppBootstrapServiceProvider extends ServiceProvider
{
	protected $commands = [
		'GTCrais\LaravelAppBootstrap\Console\Commands\Setup',
		'GTCrais\LaravelAppBootstrap\Console\Commands\GenerateKey'
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
		if ($this->providerRegistrationRequired() && $this->app->environment() != 'production') {
			$this->app->register('Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider');
		}
		$this->app->register('PhanAn\CascadingConfig\CascadingConfigServiceProvider');

		$this->commands($this->commands);
	}

	protected function providerRegistrationRequired()
	{
		$fullVersion = explode(".", $this->app->version());
		$majorVersion = $fullVersion[0];
		$minorVersion = $fullVersion[1];

		if ($majorVersion == 5 && $minorVersion < 5) {
			return true;
		}

		return false;
	}
}
