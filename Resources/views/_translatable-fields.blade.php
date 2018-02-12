<ul class="nav nav-tabs">
    @foreach ($languages as $key => $language)
        <li class="{{ $loop->first ? 'active' : '' }}">
            <a href="#crud-{{$language->iso_code}}" data-toggle="tab" aria-expanded="true">
                {{ ucfirst($language->title) }}
            </a>
        </li>
    @endforeach
</ul>
<div class="tab-content tab-content-bordered">
    @foreach ($languages as $key => $language)
        <div class="tab-pane fade in {{ $loop->first ? 'active' : '' }}" id="crud-{{$language->iso_code}}">
            @foreach($model->getTranslatableFields() as $isoCode => $fields)
                @if($language->iso_code == $isoCode)
                    @foreach($fields as $field => $type)
                        <fieldset class="form-group form-group-lg {{ $errors->has($isoCode.'.'.$field) ? 'form-message-light has-error has-validation-error' : '' }}">
                            <label for="{{ $field }}">{{ title_case(str_replace('_', ' ', $field)) }}</label>

                            @include('crud::fields.' . $type, [
                                    'attributes' => [
                                        'id' => $isoCode.'-'.$field,
                                        'class' => 'form-control',
                                        'autocomplete' => 'off',
                                        'name' => $isoCode.'['.$field.']'
                                    ],
                                    'value' => old($language->iso_code.'.'.$field, $model->hasTranslation($isoCode) ? $model->translate($isoCode)->$field : '')
                                ])

                            @if ($errors->has($isoCode.'.'.$field))
                                <div id="validation-message-light-error" class="form-message validation-error">
                                    @foreach ($errors->get($isoCode.'.'.$field) as $message)
                                        {{ $message }} <br>
                                    @endforeach
                                </div>
                            @endif
                        </fieldset>
                    @endforeach
                @endif
            @endforeach
        </div>
    @endforeach
</div>