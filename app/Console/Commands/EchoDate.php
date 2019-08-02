<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class EchoDate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'echo:date';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '显示当前日期时间';

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
        $time = date('Y-m-d H:i:s'). "\n";
        file_put_contents("/tmp/laravel_cron.log",$time,FILE_APPEND);
    }
}
