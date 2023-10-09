<?php

namespace App\Filament\Resources\TenantRequestResource\Pages;

use App\Filament\Resources\TenantRequestResource;
use Filament\Resources\Pages\ManageRecords;

class ManageTenantRequests extends ManageRecords
{
    protected static string $resource = TenantRequestResource::class;

    public function mount(): void
    {
        abort_unless(auth()->user()->is_admin, 403);
    }
}
