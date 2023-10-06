<?php

namespace App\Filament\Resources\PriorityResource\Pages;

use App\Filament\Resources\PriorityResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManagePriorities extends ManageRecords
{
    protected static string $resource = PriorityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
