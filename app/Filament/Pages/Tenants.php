<?php

namespace App\Filament\Pages;

use App\Models\Team;
use App\Models\User;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class Tenants extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.tenants';

    protected static bool $shouldRegisterNavigation = false;

    public function mount(): void
    {
        abort_unless(auth()->user()->is_admin, 403);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Team::query()->whereIsSuper(false))
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('members.name')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime(),
                TextColumn::make('updated_at')
                    ->dateTime(),
            ])
            ->striped()
            ->defaultSort('created_at', 'desc')
            ->actions([
                Action::make('addNewMember')
                    ->icon('heroicon-o-user-plus')
                    ->modalHeading(fn (Team $team) => 'Add new member to '.$team->name)
                    ->form([
                        Grid::make()
                            ->schema([
                                TextInput::make('name')
                                    ->required(),

                                TextInput::make('email')
                                    ->unique(table: User::class)
                                    ->required()
                                    ->email(),

                                TextInput::make('password')
                                    ->required()
                                    ->password(),

                                Checkbox::make('is_team_owner'),
                            ]),
                    ])
                    ->action(function (array $data, Team $record): void {
                        $user = User::create($data);

                        DB::table('team_user')->insert([
                            'team_id' => $record->id,
                            'user_id' => $user->id,
                        ]);

                        Notification::make()
                            ->title('Members added to '.$record->name)
                            ->success()
                            ->send();
                    }),

                Action::make('delete')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Delete tenant?')
                    ->modalDescription('Deleting tenant will delete all data')
                    ->action(function (Team $record) {
                        $record->members()->delete();

                        $record->members()->detach();

                        $record->delete();

                        Notification::make()
                            ->title('Tenant deleted successfully')
                            ->success()
                            ->send();
                    }),
            ]);
    }
}
