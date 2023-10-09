<?php

namespace App\Filament\Pages;

use App\Models\Team;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class Tenants extends Page implements HasTable
{
    use InteractsWithTable;

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
            ->filters([
                // ...
            ])
            ->actions([
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
            ])
            ->bulkActions([
                // ...
            ]);
    }
}
