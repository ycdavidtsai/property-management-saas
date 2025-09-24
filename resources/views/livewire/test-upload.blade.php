
<div>
    <form>
        <input type="file" wire:model="photo">
        <div wire:loading wire:target="photo">Uploading...</div>
        @if ($photo)
            <div>Preview: {{ $photo->getClientOriginalName() }}</div>
        @endif
    </form>
</div>

