<div class="flex items-center gap-2">
    <img
        src="{{ $photo ? Storage::disk('public')->url($photo) : 'https://ui-avatars.com/api/?name='.urlencode($name) }}"
        class="w-8 h-8 rounded-full object-cover"
        style="border: 2px solid {{ $color ?? '#e5e7eb' }};"
        alt="{{ $name }}"
    />
    <span>{{ $name }}</span>
</div>
