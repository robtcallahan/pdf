<div class="">
    <div class="brai-day-time-region">
        {{ $meeting->time_formatted }} - {{ $meeting->regions_formatted }}
    </div>
    <div class="brai-day-name">
        {{ $meeting->name }}
    </div>
    <div>
        @if ($meeting->location && $meeting->location !== $meeting->name)
            {{ $meeting->location }}
        @endif
    </div>
    <div class="brai-day-types">
        {{ $meeting->address }} ({{ implode(', ', $meeting->types) }})
    </div>
</div>
