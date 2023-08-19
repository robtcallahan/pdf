@php
    // TODO: need to pass width from controller

    use Illuminate\Support\Facades\Log;

    $page_margin = 18;
    $footer_height = 20;

    $row = 1;
    $column  = 1;
    $num_column_lines = 0;

    $today = date("F Y");

    define('LINES_PER_COLUMN', get_lines_per_column($height));
    echo LINES_PER_COLUMN;
@endphp
    <!DOCTYPE html>
<html lang="en">

<head>
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

        .legend:after {
            page-break-after: always;
        }
        .legend > div {
            font-size: 14px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 2px;
            padding-top: 6px;
            width: 50%;
        }
        .legend > div:last-child {
            border-bottom: none;
        }
        .legend > div span {
            display: inline-block;
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

        .brai-day {
            border: 1px solid black;
            margin: 20px 0;
            padding: 4px;
            text-align: center;
        }
        .brai-day h1 {
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

        .brai-day-time-region {
            font-size: 14px;
            font-weight: bold;
            text-decoration: underline;
            text-decoration-thickness: 2px;
            margin-bottom: 1px;
        }
        .brai-day-name {
            font-weight: bold;
        }
        .brai-day-types {
            margin-bottom: 10px;
        }

        /* Clear floats after the columns */
        .row:after {
            content: "";
            display: table;
            clear: both;
            page-break-after: always;
        }
        .row:last-child {
            page-break-after: unset;
        }
        .column {
            float: left;
            width: 30%;
            margin: 0 10px 10px;
        }

    </style>
</head>

<body>
@if ($numbering !== false)
    <footer>{{ $today  }}</footer>
@endif
<main>
    @if (in_array('legend', $options))
        @include('legend', compact('types_in_use', 'types'))
        <div class="row"></div>
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
            <div class="row"><h1 class="brai-day">{{ strtoupper($day) }}</h1><div class="column">
            @php
                list ($row, $column, $num_column_lines) = check_new_row_column($row, $column, $num_column_lines, LINES_PER_COLUMN);
                $num_column_lines++;
            @endphp

            @foreach ($regions as $region => $meetings)
                @php
                    list ($row, $column, $num_column_lines) = check_new_row_column($row, $column, $num_column_lines, LINES_PER_COLUMN, $day);
                    echo '<div class="region">';
                    if ($region) {
                        echo '<h3>' . $region . '</h3></div>';
                        $num_column_lines++;
                    }
                @endphp

                @foreach ($meetings as $meeting)
                    @php
                        $num_meeting_lines = get_num_meeting_lines($meeting);
                        list ($row, $column, $num_column_lines) = check_new_row_column($row, $column, $num_column_lines, LINES_PER_COLUMN, $day);
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
                <?php $num_column_lines++; ?>
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
            @if ($loop->first)
                @php
                    printf('<div class="row"><div class="column"><h1 class="brai-day">%s</h1>', strtoupper($day));
                    $column = 1;
                    $num_column_lines = 1;
                @endphp
            @else
                @php
                    printf('<h1 class="brai-day">%s</h1>', strtoupper($day));
                    $num_column_lines++;
                @endphp
            @endif

            @php
                list ($row, $column, $num_column_lines) = check_new_row_column($row, $column, $num_column_lines, LINES_PER_COLUMN);
                $num_column_lines++;
            @endphp
            @foreach ($meetings as $meeting)
                @php
                    $num_meeting_lines = get_num_meeting_lines($meeting);
                    list ($row, $column, $num_column_lines) = check_new_row_column($row, $column, $num_column_lines, LINES_PER_COLUMN, $day);
                    $num_column_lines += $num_meeting_lines;
                @endphp
                @include('brai-meeting', compact('meeting'))
            @endforeach
        @endforeach
        <?php echo "</div></div>"; ?>
    @endif
</main>
</body>
</html>
