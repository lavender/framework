<table class="u-max-full-width">

    <thead>

    <tr>

        @foreach($columns as $column)

            <th>{{$column}}</th>

        @endforeach

    </tr>

    </thead>

    <tbody>

    @foreach($rows as $row)

        <tr>

            @foreach($row->toArray() as $key => $value)

                <td>{{ $row->backendValue($key) }}</td>

            @endforeach

        </tr>

    @endforeach

    </tbody>

</table>