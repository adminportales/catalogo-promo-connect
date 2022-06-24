<div>
    <form wire:submit.prevent="save" class="w-100 border border-primary text-center p-2">
        <input type="file" wire:model="photos" multiple accept="image/*">

        @error('photos.*') <span class="error">{{ $message }}</span> @enderror

        <button type="submit">Save Photo</button>
    </form>
    <div wire:loading wire:target="photos">Uploading...</div>
    <div wire:loading wire:target="save">Saving...</div>
</div>
