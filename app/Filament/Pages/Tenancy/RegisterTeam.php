<?php

namespace App\Filament\Pages\Tenancy;

use App\Models\Team;
use App\Models\TenantRequest;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Pages\Tenancy\RegisterTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class RegisterTeam extends RegisterTenant
{
    public $isAlreadyRequested = false;

    public function mount(): void
    {
        abort_unless(static::canView(), 404);

        $this->form->fill();

        $req = TenantRequest::where('user_id', auth()->id())->first();

        if ($req) {
            $this->isAlreadyRequested = true;
        }
    }

    protected static string $view = 'filament.pages.tenancy.register-team';

    public static function getLabel(): string
    {
        return auth()->user()->is_admin
            ? 'Register Tenant'
            : 'No Team found!';
    }

    public static function getSlug(): string
    {
        return 'new-tenant';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->unique()
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),

                TextInput::make('slug')
                    ->unique()
                    ->disabled()
                    ->dehydrated(),

                Select::make('members')
                    ->required()
                    ->relationship(name: 'members', titleAttribute: 'name')
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        TextInput::make('name')
                            ->required(),

                        TextInput::make('email')
                            ->unique()
                            ->required()
                            ->email(),

                        TextInput::make('password')
                            ->required()
                            ->password(),

                        Checkbox::make('is_team_owner'),
                    ]),
            ]);
    }

    protected function handleRegistration(array $data): Model
    {
        $team = Team::create([
            'name' => $data['name'],
            'slug' => $data['slug'],
        ]);

        $team->members()->attach($data['members']);

        Notification::make()
            ->title('New team '.$team->name.' created successfully')
            ->success()
            ->send();

        return $team;
    }

    protected function getRedirectUrl(): ?string
    {
        return route('filament.admin.tenant');
    }

    public function requestToJoinNewTeam()
    {
        TenantRequest::create([
            'user_id' => auth()->id(),
        ]);

        Notification::make()
            ->title('Joining request has been submitted')
            ->success()
            ->send();

        $this->isAlreadyRequested = true;
    }
}
