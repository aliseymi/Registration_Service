<?php

namespace App\Console\Commands;

use App\Models\MagicToken;
use Illuminate\Console\Command;

class ClearMagicTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auth:clear-tokens';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear expired magic tokens';

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
     * @return int
     */
    public function handle()
    {
        return MagicToken::expired()->delete();
    }
}
