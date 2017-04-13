<?php

namespace GTCrais\LaravelAppBootstrap\Console\Commands;

use Illuminate\Foundation\Console\KeyGenerateCommand;

class GenerateKey extends KeyGenerateCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lab:generate-key';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates new application key';

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
		// Generate key
		$key = $this->generateRandomKey();

		file_put_contents(config_path('app.php'), preg_replace(
			"/'key' => '.*'/",
			"'key' => '" . $key . "'",
			file_get_contents(config_path('app.php'))
		));

		$this->info("New application key generated!");
    }
}
