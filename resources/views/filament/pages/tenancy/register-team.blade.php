<x-filament-panels::page.simple>
    @if (auth()->user()->is_admin)
        <x-filament-panels::form wire:submit="register">
            {{ $this->form }}

            <x-filament-panels::form.actions
                :actions="$this->getCachedFormActions()"
                :full-width="$this->hasFullWidthFormActions()"
            />
        </x-filament-panels::form>
    @else
        <x-filament::fieldset>
            <x-slot name="label">
                Oops!
            </x-slot>
            
            Looks like you don't have a team to join!, please contact admin for further details.
        </x-filament::fieldset>
    @endif
</x-filament-panels::page.simple>
