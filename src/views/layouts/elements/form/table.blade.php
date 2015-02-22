<table class="u-max-full-width">

    <thead>

        <tr>

            <th>{{ Form::checkbox('all'); }}</th>

            @foreach($headers as $header)

                <th>{{$header}}</th>

            @endforeach

        </tr>

    </thead>

    <tbody>

    @foreach($rows as $i => $row)

        <tr>

            <td>{{ Form::checkbox($i); }}</td>

            @foreach($row as $key => $value)

                <td>{{ $value }}</td>

            @endforeach

        </tr>

    @endforeach

    </tbody>

</table>