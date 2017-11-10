@foreach($fields as $field => $type)
    <fieldset class="form-group form-group-lg {{ $errors->has($field) ? 'form-message-light has-error has-validation-error' : '' }}">
        <label for="{{ $field }}">{{ title_case(str_replace('_', ' ', $field)) }}</label>

        @include('crud::fields.' . $type, [
            'attributes' => [
                    'id' => $field,
                    'class' => 'form-control',
                    'autocomplete' => 'off'
                ]
            ])

        @if ($errors->has($field))
            <div id="validation-message-light-error" class="form-message validation-error">
                @foreach ($errors->get($field) as $message)
                    {{ $message }} <br>
                @endforeach
            </div>
        @endif
    </fieldset>
@endforeach
