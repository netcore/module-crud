@foreach( $fields as $field => $type )
    <fieldset class="form-group form-group-lg{{$errors->has($field) ? ' form-message-light has-error has-validation-error' : ''}}">
        <label for="{{$field}}">{{title_case(str_replace('_', ' ', $field))}}</label>

        <?php
            $attributes = ['id' => $field, 'class' => 'form-control', 'autocomplete' => 'off' ];

            if( $type == 'password' ){
                echo Form::$type($field, $attributes);
            }

            else if( in_array($type, ['boolean','select']) ){
                echo Form::select($field,[1 => 'yes', 2 => 'no'], null, $attributes);
            }
            else if($type == 'ritchtext') {
                $attributes['class'] = 'form-control ritch-textarea';

                echo Form::textarea($field, null, $attributes);
            }
            else {
                echo Form::$type($field, null, $attributes);
            }
        ?>
        @if ($errors->has($field))
            <div id="validation-message-light-error" class="form-message validation-error">
                @foreach ($errors->get($field) as $message)
                    {{$message}} <br>
                @endforeach
            </div>
        @endif
    </fieldset>
@endforeach