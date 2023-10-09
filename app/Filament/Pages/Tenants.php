<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Tenants extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.tenants';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public function mount(): void
    {
        abort_unless(auth()->user()->is_admin, 403);
    }
}
