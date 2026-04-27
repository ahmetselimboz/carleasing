<?php

namespace App\Observers;

use App\Jobs\SendLeadCreatedEmailsJob;
use App\Models\Message;
use App\Models\RentalRequest;
use App\Models\WeCallYou;
use Illuminate\Database\Eloquent\Model;

class LeadSourceObserver
{
    public function created(Model $model): void
    {
        $type = match (true) {
            $model instanceof RentalRequest => 'rental_request',
            $model instanceof Message => 'message',
            $model instanceof WeCallYou => 'we_call_you',
            default => null,
        };

        if ($type === null) {
            return;
        }

        SendLeadCreatedEmailsJob::dispatch($type, (int) $model->getKey())->afterCommit();
    }
}
