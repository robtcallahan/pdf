<?php

// TODO: correct sheet size
define('LinesPerColumn', round(6.2 * 5.5));
const MaxColumns = 3;
const MaxCharactersPerLine = 37;

// determine the number of rows the meeting text will occupy and add that to the number of lines in the column
function get_num_meeting_lines(mixed $meeting) : int {
    $meeting_location = $meeting->location && $meeting->location !== $meeting->name ? $meeting->location . "|" : "";
    $wrapped_meeting_string = wordwrap($meeting->name . "|" . $meeting_location . $meeting->address . " (" . implode(', ', $meeting->types) . ")",
        MaxCharactersPerLine, "|");
    // adding 2: one for the date - region and one because the count of "|" doesn't account for the last string
    return substr_count($wrapped_meeting_string, "|") + 2;
}

function check_new_row_column(int $row, int $column, int $num_column_lines, string $day = ""): array {
    if (need_new_column($num_column_lines)) {
        if (is_last_column($column)) {
            if ($day != "") {
                echo new_day($day);
            } else {
                echo new_row();
            }
            $row++;
            $column = 1;
        } else {
            echo new_column();
            $column++;
        }
        $num_column_lines = 0;
    }
    return array($row, $column, $num_column_lines);
}

function need_new_column(int $num_column_lines) : bool {
    if ($num_column_lines >= LinesPerColumn) {
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
    return '</div></div><div class="row"><h1 class="meeting-day">' . strtoupper($day) . ' MEETINGS</h1><div class="column">';
}

function new_row() : string {
    return '</div></div><div class="row"><div class="column">';
}

function new_column() : string {
    return '</div><div class="column">';
}

function new_day_row() : string {
    return '</div>' . new_row() . '<div class="day">';
}

function new_day_column() : string {
    return '</div>' . new_column() . '<div class="day">';
}

function new_region_row() : string {
    return '</div>' . new_day_row() . '<div class="region">';
}

function new_region_column() : string {
    return '</div>' . new_day_column() . '<div class="region">';
}
