<table class="u-max-full-width">

    <thead>

    <tr>

        <th />

        @foreach($headers as $header)

            <th>{{$header}}</th>

        @endforeach

    </tr>

    </thead>

    <tbody>

    @foreach($collection as $item)

        <tr>
            <td><input type='checkbox' name='item[]' value='{{{ $item->id }}}'/></td>

            @foreach($item->toArray() as $key => $value)

                <td>{{ $item->backendValue($key) }}</td>

            @endforeach

        </tr>

    @endforeach

    </tbody>

</table>