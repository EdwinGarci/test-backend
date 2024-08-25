<?php

namespace App\Jobs;

use App\Mail\VouchersCreatedMail;
use App\Mail\VouchersFailedMail;
use App\Models\User;
use App\Services\VoucherService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class ProcessVouchersUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $xmlContents;
    protected User $user;

    /**
     * Create a new job instance.
     * 
     * @param array $xmlContents
     * @param User $user
     * @return void
     */
    public function __construct(array $xmlContents, User $user)
    {
        $this->xmlContents = $xmlContents;
        $this->user = $user;
    }

    /**
     * Execute the job.
     * 
     * @return void
     */
    public function handle(): void
    {
        $voucherService = app(VoucherService::class);

        $successfulVouchers = [];
        $failedVouchers = [];

        foreach ($this->xmlContents as $xmlContent) {
            try {
                $voucher = $voucherService->storeVoucherFromXmlContent($xmlContent, $this->user);
                $successfulVouchers[] = $voucher;
            } catch (\Exception $e) {
                $failedVouchers[] = [
                    'error' => $e->getMessage(),
                    'content' => $xmlContent,
                ];
            }
        }

        Mail::to($this->user->email)->send(new VouchersCreatedMail($successfulVouchers, $this->user));
        Mail::to($this->user->email)->send(new VouchersFailedMail($failedVouchers, $this->user));
    }
}
