<style>
    .meeting-day-time-region {
        font-size: 14px;
        font-weight: bold;
        text-decoration: underline;
        text-decoration-thickness: 2px;
        /*border-bottom: 2px solid black;*/
        margin-bottom: 1px;
    }
    .meeting-day-name {
        font-weight: bold;
    }
    .meeting-day-types {
        margin-bottom: 10px;
    }

</style>

<div class="">
    <div class="meeting-day-time-region">
        {{ $meeting->time_formatted }} - {{ $meeting->regions_formatted }}
    </div>
    <div class="meeting-day-name">
        {{ $meeting->name }}
    </div>
    <div>
        @if ($meeting->location && $meeting->location !== $meeting->name)
            {{ $meeting->location }}
        @endif
    </div>
    <div class="meeting-day-types">
        {{ $meeting->address }} ({{ implode(', ', $meeting->types) }})
    </div>
{{--    <div class="meeting-day-types">--}}
{{--        ({{ implode(', ', $meeting->types) }})--}}
{{--    </div>--}}
</div>
