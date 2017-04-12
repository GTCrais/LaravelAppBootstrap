<?php

namespace GTCrais\LaravelAppBootstrap\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class Setup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lab:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bootstraps Laravel Application';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
		// Copy files
		$this->callSilent('vendor:publish', [
			'--provider' => 'GTCrais\LaravelAppBootstrap\LaravelAppBootstrapServiceProvider',
			'--force' => true
		]);

		// Generate IDE helper file
		if (app()->environment() != 'production') {
			$this->callSilent('ide-helper:generate');
		}

		// Get app config info
		$appName = $this->ask('Application name', 'App');
		$devUrl = $this->ask("Development URL ('http://' optional)", 'http://localhost');
		$timezone = $this->ask('Timezone', 'Europe/Zagreb');
		$locale = $this->ask('Locale', 'en');

		if (!Str::startsWith($devUrl, ['http://', 'https://'])) {
			$devUrl = 'http://' . $devUrl;
		}

		$database = $this->ask('Development database name');
		$databaseUsername = $this->ask('Development database username');
		$databasePassword = $this->ask('Development database password', false);
		$databasePrefix = $this->ask('Development database prefix', false);

		// Replace env() calls in /config/app.php config file
		$this->replaceAppEnvCalls($appName, $timezone, $locale);

		// Add Development URL to /config.localdev/app.php
		$this->addDevelopmentUrl($devUrl);

		// Add Development DB config to /config.localdev/database.php
		$this->addDevelopmentDatabaseConfig($database, $databaseUsername, $databasePassword, $databasePrefix);

		// Delete .env files
		$this->deleteEnv();

		// Add /bootstrap/env.php to .gitignore
		$this->updateGitIgnore();

		$this->info("Your application has been bootstrapped!");
    }

	protected function replaceAppEnvCalls($appName, $timezone, $locale)
	{
		try {
			file_put_contents(config_path('app.php'), preg_replace(
				"/'name' => env\(.+\)/",
				"'name' => '" . $appName . "'",
				file_get_contents(config_path('app.php'))
			));

			file_put_contents(config_path('app.php'), preg_replace(
				"/'env' => env\(.+\)/",
				"'env' => require(base_path('bootstrap/env.php'))",
				file_get_contents(config_path('app.php'))
			));

			file_put_contents(config_path('app.php'), preg_replace(
				"/'debug' => env\(.+\)/",
				"'debug' => false",
				file_get_contents(config_path('app.php'))
			));

			file_put_contents(config_path('app.php'), preg_replace(
				"/'url' => env\(.+\)/",
				"'url' => ''",
				file_get_contents(config_path('app.php'))
			));

			file_put_contents(config_path('app.php'), preg_replace(
				"/'timezone' => '.+'/",
				"'timezone' => '" . $timezone . "'",
				file_get_contents(config_path('app.php'))
			));

			file_put_contents(config_path('app.php'), preg_replace(
				"/'locale' => '.+'/",
				"'locale' => '" . $locale . "'",
				file_get_contents(config_path('app.php'))
			));

			$key = config('app.key');

			file_put_contents(config_path('app.php'), preg_replace(
				"/'key' => env\(.+\)/",
				"'key' => '" . $key . "'",
				file_get_contents(config_path('app.php'))
			));

			file_put_contents(config_path('app.php'), preg_replace(
				"/'log' => env\(.+\)/",
				"'log' => 'single'",
				file_get_contents(config_path('app.php'))
			));

			file_put_contents(config_path('app.php'), preg_replace(
				"/'log_level' => env\(.+\)/",
				"'log_level' => 'debug'",
				file_get_contents(config_path('app.php'))
			));
		} catch (\Exception $e) {
			$this->info('env() calls could not be replaced.');
		}
	}

	protected function addDevelopmentUrl($devUrl)
	{
		try {
			file_put_contents(base_path('config.localdev/app.php'), preg_replace(
				"/'url' => ''/",
				"'url' => '" . $devUrl . "'",
				file_get_contents(base_path('config.localdev/app.php'))
			));
		} catch (\Exception $e) {
			$this->info('Development URL could not be added');
		}
	}

	protected function addDevelopmentDatabaseConfig($dbName, $dbUserName, $dbPassword, $dbPrefix)
	{
		try {
			file_put_contents(base_path('config.localdev/database.php'), preg_replace(
				"/'database' => '.*'/",
				"'database' => '" . $dbName . "'",
				file_get_contents(base_path('config.localdev/database.php'))
			));

			file_put_contents(base_path('config.localdev/database.php'), preg_replace(
				"/'username' => '.*'/",
				"'username' => '" . $dbUserName . "'",
				file_get_contents(base_path('config.localdev/database.php'))
			));

			file_put_contents(base_path('config.localdev/database.php'), preg_replace(
				"/'password' => '.*'/",
				"'password' => '" . $dbPassword . "'",
				file_get_contents(base_path('config.localdev/database.php'))
			));

			file_put_contents(base_path('config.localdev/database.php'), preg_replace(
				"/'prefix' => '.*'/",
				"'prefix' => '" . $dbPrefix . "'",
				file_get_contents(base_path('config.localdev/database.php'))
			));
		} catch (\Exception $e) {
			$this->info('Development database config could not be added.');
		}
	}

	protected function deleteEnv()
	{
		try {
			if (file_exists(base_path('.env'))) {
				unlink(base_path('.env'));
			}
			if (file_exists(base_path('.env.example'))) {
				unlink(base_path('.env.example'));
			}
		} catch (\Exception $e) {
			$this->info('.env file could not be deleted.');
		}
	}

	protected function updateGitIgnore()
	{
		try {
			if (file_exists(base_path('.gitignore'))) {
				$file = fopen(base_path('.gitignore'), 'r');
				$isUpdated = false;
				$lastLine = "";

				while (!feof($file)) {
					$lastLine = fgets($file);
					if ($lastLine == '/bootstrap/env.php') {
						$isUpdated = true;
					}
				}

				fclose($file);

				if ($isUpdated) {
					return;
				}

				$append = '/bootstrap/env.php';
				if ($lastLine != "") {
					$append = "\n" . $append;
				}

				file_put_contents(base_path('.gitignore'), $append, FILE_APPEND);
			}
		} catch (\Exception $e) {
			$this->info('.gitignore file could not be updated.');
		}
	}
}
