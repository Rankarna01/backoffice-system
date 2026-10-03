<?php

namespace App\Filament\Resources\Billing\Orders\Pages;

use App\Filament\Resources\Billing\Orders\OrderResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;
}
