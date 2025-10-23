<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Appearance')" :subheading=" __('Update the appearance settings for your account')">
        <form wire:submit.prevent="save">
            <flux:radio.group x-data variant="segmented" wire:model="appearance" wire:change="save">
                <flux:radio value="light" icon="sun">{{ __('Light') }}</flux:radio>
                <flux:radio value="dark" icon="moon">{{ __('Dark') }}</flux:radio>
                <flux:radio value="system" icon="computer-desktop">{{ __('System') }}</flux:radio>
            </flux:radio.group>
            <div class="mt-4">
                <x-button type="submit">
                    {{ __('Save Changes') }}
                </x-button>
            </div>
        </form>
    </x-settings.layout>
</section>