{{ Form::open( $options ) }}

    @foreach( $groups as $group => $fields )

        <fieldset name="{{{ $group }}}">

            @foreach( $fields as $class => $field )

                <div class="field {{{ $class }}}">

                    {{ $field }}

                </div>

            @endforeach

        </fieldset>

    @endforeach

{{ Form::close() }}