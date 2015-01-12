{{ Form::open( $options ) }}

    @foreach( $fields as $class => $field )

        <div class="field {{{ $class }}}">

            {{ $field }}

        </div>

    @endforeach

{{ Form::close() }}