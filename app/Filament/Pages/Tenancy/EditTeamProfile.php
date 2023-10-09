<?php

namespace App\Filament\Pages\Tenancy;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Pages\Tenancy\EditTenantProfile;
use Illuminate\Support\Str;

class EditTeamProfile extends EditTenantProfile
{
    public static function getLabel(): string
    {
        return 'Edit tenant';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),

                TextInput::make('slug')
                    ->unique(ignoreRecord: true)
                    ->disabled()
                    ->dehydrated(),
            ]);
    }

    protected function getRedirectUrl(): ?string
    {
        return route('filament.admin.tenant');
    }
}
