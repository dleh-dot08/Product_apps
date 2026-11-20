<?php

namespace App\Observers;

use App\Jobs\QueueDeliveryOrderCost;
use App\Models\DeliveryAssignment;

class DeliveryAssignmentObserver
{
    public function saved(DeliveryAssignment $delivery): void
    {
        QueueDeliveryOrderCost::dispatch((string) $delivery->id)->afterCommit();
    }
}
