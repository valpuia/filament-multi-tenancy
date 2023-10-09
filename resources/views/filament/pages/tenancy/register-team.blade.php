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
            
            Looks like you don't have a team to join!
            
            @if ($isAlreadyRequested)
                Please wait for authorised person to add you in a team.
            @else
                Please click below button to request for joining new team.

                <div class="mt-2">
                    <x-filament::button wire:click="requestToJoinNewTeam">
                        Requesting to join
                    </x-filament::button>
                </div>
            @endif
        </x-filament::fieldset>
    @endif
</x-filament-panels::page.simple>
