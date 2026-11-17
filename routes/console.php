<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use App\Models\Listing;
use App\Models\UserTariff;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schedule;

Artisan::command('listings:check-expired', function () {
    $this->info('Checking for expired listing subscriptions...');
    
    // Find listings that are active but no longer paid
    $activeListings = Listing::where('status', 'active')->get();
    $deactivatedCount = 0;
    
    foreach ($activeListings as $listing) {
        if (!$listing->is_paid) {
            $listing->status = 'pending_payment';
            $listing->save();
            $deactivatedCount++;
            $this->comment("Deactivated listing #{$listing->id} due to expired subscription.");
        }
    }
    
    // Update expired UserTariff statuses just for database cleanliness
    UserTariff::where('status', 'active')
        ->where('expires_at', '<', Carbon::now())
        ->update(['status' => 'expired']);

    $this->info("Completed. Deactivated {$deactivatedCount} listings.");
})->purpose('Deactivate active listings with expired subscriptions');

// Schedule the command to run daily
Schedule::command('listings:check-expired')->daily();
Schedule::command('sitemap:generate')->daily();


