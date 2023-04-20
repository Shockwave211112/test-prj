<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Reports;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:report';

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
        $date = Carbon::yesterday()->toDateString();
        $orders = Order::where([['closed_at', 'like', "%{$date}%"], ['is_closed', '=', 'true']])->get();
        $total_orders = $orders->count();
        $total_profit = $orders->sum('total_cost');

        Reports::create(['total_orders' => $total_orders, 'total_profit' => $total_profit]);
    }
}
