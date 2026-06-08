<x-filament-panels::page>
    <div
        x-data="{
            autoSave: $wire.entangle('autoSave').live,
            timer: null,
            scheduleSave() {
                if (!this.autoSave) return;
                clearTimeout(this.timer);
                this.timer = setTimeout(() => $wire.save(), 2000);
            }
        }"
        @input="scheduleSave()"
        @change="scheduleSave()"
    >
        <form id="save" wire:submit="save">
            {{ $this->form }}
        </form>
    </div>
</x-filament-panels::page>
