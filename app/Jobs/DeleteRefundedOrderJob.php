<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class DeleteRefundedOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;

    /**
     * Create a new job instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (!$this->order || $this->order->status !== 'refunded') {
            return;
        }

        DB::transaction(function () {
            foreach ($this->order->items as $item) {
                if ($item->book && is_numeric($item->qty)) {
                    $item->book->increment('stock', (int) $item->qty);
                }
            }

            $this->order->delete();
        });
    }
}
