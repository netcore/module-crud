@foreach($model->getFields() as $field => $type)
    <fieldset
            class="form-group form-group-lg {{ $errors->has($field) ? 'form-message-light has-error has-validation-error' : '' }}">
        <label for="{{ $field }}">{{ title_case(str_replace('_', ' ', $field)) }}</label>

        @include('crud::fields.' . $type, [
            'attributes' => [
                    'id' => $field,
                    'class' => 'form-control',
                    'autocomplete' => 'off'
                ]
            ])

        @if($type === 'file')

            @if($model->exists)
                <div class="media">
                    <div class="media-left">
                        @if(is_image($model->$field->url()))
                            <a href="{{ extension_image($model->$field->url()) }}" target="_blank">
                                <img src="{{ extension_image($model->$field->url()) }}" class="media-object" style="width:70px">
                            </a>
                        @else
                            <img src="{{ extension_image($model->$field->url()) }}" class="media-object">
                        @endif
                    </div>
                    <div class="media-body">
                        <h4 class="media-heading">{{ basename($model->$field->url()) }}</h4>
                    </div>
                </div>
            @endif
        @endif

        @if ($errors->has($field))
            <div id="validation-message-light-error" class="form-message validation-error">
                @foreach ($errors->get($field) as $message)
                    {{ $message }} <br>
                @endforeach
            </div>
        @endif
    </fieldset>
@endforeach
