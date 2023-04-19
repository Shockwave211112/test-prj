<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Reports;
use App\Models\ResetPin;
use Carbon\Carbon;
use Illuminate\Console\Command;

class PinClearCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pin:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $reset_pin = ResetPin::where('expires_at', '<', Carbon::now())->get();
        foreach ($reset_pin as $item) {
            $item->delete();
        }
    }
}
