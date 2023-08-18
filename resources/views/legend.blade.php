<div class="legend">
    <h1>MEETING TYPES</h1>
    @foreach ($types_in_use as $type)
        <div class="type-row">
            <table style="width: 100%">
                <tr>
                    <td style="width: 20%">{{ $type }}</td>
                    <td>{{ $types[$type] }}</td>
                </tr>
            </table>
        </div>
    @endforeach
</div>
