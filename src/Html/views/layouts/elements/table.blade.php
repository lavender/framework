<table id="{{{ $id }}}" class="u-max-full-width">

    <thead>

        <tr>

            @foreach($headers as $header)

                <th>{{$header}}</th>

            @endforeach

        </tr>

    </thead>

    <tbody>

    @foreach($rows as $row)

        <tr>

            @foreach($row as $key => $value)

                <td>{{ $value }}</td>

            @endforeach

        </tr>

    @endforeach

    </tbody>

</table>