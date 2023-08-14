@php
    // TODO: need to pass width from controller

    use Illuminate\Support\Facades\Log;

    $page_margin = 18;
    $footer_height = 20;

    $row = 1;
    $column  = 1;
    $num_column_lines = 0;

@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <title>AA Meetings Schedule</title>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <style>
        @page {
            margin: {{ $page_margin }}px;

            @if ($numbering !== false)
                 margin-bottom: {{ $footer_height + $page_margin }}px;
        @endif

        }

        body {
            color: black;
            counter-increment: page 1;
            counter-reset: page{{ $numbering - 1 }};
            font-family: {{ $font }};
            font-size: 12px;
        }

        h1 {
            border-bottom: 0.5px solid black;
            font-size: 16px;
            margin: 0 0 10px;
            padding-bottom: 4px;
        }

        h3 {
            font-weight: normal;
            font-size: 11px;
            margin: 1px 0 3px;
            page-break-after: avoid;
            text-decoration: underline;
            text-transform: uppercase;
        }

        .legend {
            page-break-after: always;
        }

        .legend > div {
            font-size: 9px;
            border-bottom: .5px solid #ddd;
            padding-bottom: 1.5px;
            padding-top: 6px;
        }

        .legend > div:last-child {
            border-bottom: none;
        }

        .legend > div span {
            display: inline-block;
        }

        .legend > div .type {
            width: 40px;
        }

        .meeting {
            border-spacing: 0;
            margin: 0 0 5px;
            padding: 0;
            page-break-inside: avoid;
            vertical-align: top;
            width: 100%;
        }

        .meeting td {
            margin: 0;
            padding: 0;
            vertical-align: top;
        }

        .meeting .time {
            width: 65px;
            text-align: right;
            padding-right: 5px;
        }

        .meeting .name {
            font-weight: bold;
        }

        .meeting-day {
            border: 2px solid black;
            margin: 20px 0;
            padding: 4px;
            text-align: center;
        }
        .meeting-day h1 {
            font-size: 20px;
        }

        footer {
            bottom: -{{ $footer_height }}px;
            height: {{ $footer_height }}px;
            left: 0;
            position: fixed;
            right: 0;
        }

        footer::after {
            border-top: 0.5px solid black;
            content: counter(page);
            left: 50%;
            margin-left: -20px;
            padding-top: 4px;
            position: absolute;
            text-align: center;
            width: 40px;
        }

        /* Rob C */
        .column {
            float: left;
            width: 30%;
            margin: 0 10px 10px;
        }

        /* Clear floats after the columns */
        .row:after {
            content: "";
            display: table;
            clear: both;
            page-break-after: always;
        }
    </style>
</head>

<body>
@if ($numbering !== false)
    <footer></footer>
@endif
<main>
    @if (in_array('legend', $options))
        @include('legend', compact('types_in_use', 'types'))
    @endif
    @if ($group_by === 'day-region')
        @foreach ($days as $day => $regions)
            @if (!$loop->first)
                @php
                    echo '</div></div>';
                    $row++;
                    $column = 1;
                    $num_column_lines = 0;
                @endphp
            @endif
            <div class="row"><h1 class="meeting-day">{{ strtoupper($day) }} MEETINGS</h1><div class="column">
            @php
                list ($row, $column, $num_column_lines) = check_new_row_column($row, $column, $num_column_lines);
                $num_column_lines++;
            @endphp

            @foreach ($regions as $region => $meetings)
                @php
                    list ($row, $column, $num_column_lines) = check_new_row_column($row, $column, $num_column_lines, $day);
                    echo '<div class="region">';
                    if ($region) {
                        echo '<h3>' . $region . '</h3></div>';
                        $num_column_lines++;
                    }
                @endphp

                @foreach ($meetings as $meeting)
                    @php
                        $num_meeting_lines = get_num_meeting_lines($meeting);
                        list ($row, $column, $num_column_lines) = check_new_row_column($row, $column, $num_column_lines, $day);
                        $num_column_lines += $num_meeting_lines;
                    @endphp
                    @include('meeting', compact('meeting', 'region'))
                @endforeach
            @endforeach
        @endforeach
        </div></div>
    @elseif ($group_by === 'region-day')
        @foreach ($regions as $region => $days)
            @if (!$loop->first)
                <?php list ($row, $column, $num_column_lines) = check_new_row_column($row, $column, $num_column_lines); ?>
            @else
                <div class="row"><div class="column"><div class="region"><h1>{{ $region }}</h1></div>
            @endif
            <?php $num_column_lines++ ?>
            @foreach ($days as $day => $meetings)
                @php
                    list ($row, $column, $num_column_lines) = check_new_row_column($row, $column, $num_column_lines);
                    $num_column_lines++;
                @endphp
                <h3>{{ $day }}</h3>
                @foreach ($meetings as $meeting)
                    @php
                        $num_meeting_lines = get_num_meeting_lines($meeting);
                        list ($row, $column, $num_column_lines) = check_new_row_column($row, $column, $num_column_lines);
                        $num_column_lines += $num_meeting_lines;
                    @endphp
                    @include('meeting', compact('meeting', 'region'))
                @endforeach
            @endforeach
        @endforeach
        </div></div>
    @else
        @foreach ($days as $day => $meetings)
            @if (!$loop->first)
                @php
                    echo '</div></div>';
                    $row++;
                    $column = 1;
                    $num_column_lines = 0;
                @endphp
            @endif
            <div class="row"><h1 class="meeting-day">{{ strtoupper($day) }} MEETINGS</h1><div class="column">
            @php
                list ($row, $column, $num_column_lines) = check_new_row_column($row, $column, $num_column_lines);
                $num_column_lines++;
            @endphp
            @foreach ($meetings as $meeting)
                @php
                    $num_meeting_lines = get_num_meeting_lines($meeting);
                    list ($row, $column, $num_column_lines) = check_new_row_column($row, $column, $num_column_lines, $day);
                    $num_column_lines += $num_meeting_lines;
                @endphp
                @include('meeting-day', compact('meeting'))
            @endforeach
        @endforeach
        </div></div>
    @endif
</main>
</body>
</html>
