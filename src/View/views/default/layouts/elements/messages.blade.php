@foreach($messages as $type => $messagebag)

    <ul class="{{{ $type }}}">

        @foreach( $messagebag->all() as $message )

            <li>{{ $message }}</li>

        @endforeach

    </ul>

@endforeach