<?php

namespace App\Console\Commands\Pharmacy;

use App\Services\Pharmacy\InventoryService;
use Illuminate\Console\Command;

class SyncStockAlerts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pharmacy:sync-alerts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize stock alerts for all medicines based on current stock levels';

    /**
     * Execute the console command.
     */
    public function handle(InventoryService $inventoryService)
    {
        $this->info('Starting stock alerts synchronization...');

        $count = $inventoryService->syncStockAlerts();

        $this->info("Stock alerts synchronization complete. {$count} alerts were created or updated.");

        return Command::SUCCESS;
    }
}
