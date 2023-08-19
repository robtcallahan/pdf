<?php

// TODO: correct sheet size
//define('LinesPerColumn', round(6.2 * 5.5));
const MaxColumns = 3;
const MaxCharactersPerLine = 37;

function get_lines_per_column($height) {
    return round($height / 16);
}

// determine the number of rows the meeting text will occupy and add that to the number of lines in the column
function get_num_meeting_lines(mixed $meeting) : int {
    $meeting_location = $meeting->location && $meeting->location !== $meeting->name ? $meeting->location . "|" : "";
    $wrapped_meeting_string = wordwrap($meeting->name . "|" . $meeting_location . $meeting->address . " (" . implode(', ', $meeting->types) . ")",
        MaxCharactersPerLine, "|");
    // adding 2: one for the date - region and one because the count of "|" doesn't account for the last string
    return substr_count($wrapped_meeting_string, "|") + 2;
}

function check_new_row_column(int $row, int $column, int $num_column_lines, int $lines_per_column, string $day = ""): array {
    if (need_new_column($num_column_lines, $lines_per_column)) {
        if (is_last_column($column)) {
            echo new_row();
            if ($day != "") {
                echo new_day($day);
            }
            $row++;
            $column = 1;
            $num_column_lines = 4;
        } else {
            echo new_column();
            $column++;
            $num_column_lines = 0;
        }
    }
    return array($row, $column, $num_column_lines);
}

function need_new_column(int $num_column_lines, int $lines_per_column) : bool {
    if ($num_column_lines >= $lines_per_column) {
        return true;
    }
    return false;
}

function is_last_column(int $column) : bool {
    if ($column == MaxColumns) {
        return true;
    }
    return false;
}

function new_day(string $day) : string {
    return '<h1 class="brai-day">' . strtoupper($day) . '</h1>';
}

function new_row() : string {
    return '</div></div><div class="row"><div class="column">';
}

function new_column() : string {
    return '</div><div class="column">';
}

function new_day_row() : string {
    return printf('</div>%s<div class="day">', new_row());
}

function new_day_column() : string {
    return printf('</div>%s<div class="day">', new_column());
}

function new_region_row() : string {
    return printf('</div>%s<div class="region">', new_day_row());
}

function new_region_column() : string {
    return printf('</div>%s<div class="region">', new_day_column());
}
