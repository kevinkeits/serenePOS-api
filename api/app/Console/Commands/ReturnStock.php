<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ReturnStock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'return:stock';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $query = "SELECT ord.ID OrderID, opy.ID, opy.PaymentMethodCategory, r.Field2 PaymentMethod, 
                        r.Field3 PaymentLogo, opy.ReferenceID, opy.GopayDeepLink, opy.GrossAmount, opy.IsPaid, opy.ExpiredDate,
                        CASE WHEN (NOW() >= opy.ExpiredDate) THEN 1 ELSE 0 END IsExpired
                    FROM TR_ORDER_PAYMENT opy
                    JOIN TR_ORDER ord ON ord.PaymentID=opy.ID
                        LEFT JOIN MS_REFERENCES r ON (r.Type='PaymentMethod' AND r.Field2 LIKE CONCAT(opy.PaymentMethod,'%'))
                    WHERE opy.IsCancelled=0
                        AND ord.Status=1
                        AND ord.IsB2B = 0
                    ORDER BY ord.CreatedDate DESC";
        $data = DB::select($query);
        foreach ($data as $item) {
            if ($item->IsExpired) {
                $query = "UPDATE TR_ORDER_PAYMENT SET IsCancelled=1 WHERE ID=?";
                DB::update($query, [$item->ID]);
                $query = "UPDATE TR_ORDER SET Status=5,CancelledDate=NOW(),CancelledReason='Pembatalan otomatis, Batas waktu pembayaran telah berakhir' WHERE PaymentID=?";
                DB::update($query, [$item->ID]);

                $query = "SELECT ProductID, Qty FROM TR_ORDER_PRODUCT WHERE OrderID=?";
                $product = DB::select($query, [$item->OrderID]);
                foreach ($product as $key => $value) {
                    $query = "UPDATE MS_PRODUCT
                                SET Stock=(Stock+".$value->Qty.")
                                WHERE ID=?";
                    DB::update($query, [
                        $value->ProductID
                    ]);
                } 
            }
        }
 	\Log::info("Cron is working fine!"); 
    }
}