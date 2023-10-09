<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TenantRequestResource\Pages;
use App\Models\Team;
use App\Models\TenantRequest;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class TenantRequestResource extends Resource
{
    protected static ?string $model = TenantRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static bool $shouldRegisterNavigation = false;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->default('Requesting to join new team!'),
                Forms\Components\Select::make('user.name')
                    ->relationship('user', 'name')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Tenant name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime(),
            ])
            ->actions([
                Action::make('addToTeam')
                    ->modalHeading('Add tenant to a team?')
                    ->modalDescription('Adding tenant will clear this data!')
                    ->icon('heroicon-o-user-plus')
                    ->form([
                        Grid::make()
                            ->schema([
                                Placeholder::make('Tenant Name')
                                    ->content(fn (TenantRequest $record): string => $record->user->name),

                                Forms\Components\Select::make('team_id')
                                    ->label('Team')
                                    ->relationship('team', 'name', fn (Builder $query) => $query->whereIsSuper(false))
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name')
                                            ->unique()
                                            ->required()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(
                                                fn (Set $set, ?string $state) => $set('slug', Str::slug($state))
                                            ),

                                        Forms\Components\TextInput::make('slug')
                                            ->unique()
                                            ->disabled()
                                            ->dehydrated(),
                                    ]),
                            ]),
                    ])
                    ->action(function (array $data, TenantRequest $record): void {
                        $team = Team::find($data['team_id']);

                        $team->members()->attach($record['user_id']);

                        $record->delete();

                        Notification::make()
                            ->title('Added successfully to team '.$team->name)
                            ->success()
                            ->send();
                    }),

                Action::make('delete')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalDescription('Deleting this request will delete user credentials as well')
                    ->action(function (TenantRequest $record): void {
                        User::find($record->user_id)->delete();

                        $record->delete();

                        Notification::make()
                            ->title('Record and user deleted successfully')
                            ->success()
                            ->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageTenantRequests::route('/'),
        ];
    }
}
