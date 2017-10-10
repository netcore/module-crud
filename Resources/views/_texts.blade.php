@foreach($fields as $field => $type)
    <fieldset class="form-group form-group-lg {{ $errors->has($field) ? 'form-message-light has-error has-validation-error' : '' }}">
        <label for="{{ $field }}">{{ title_case(str_replace('_', ' ', $field)) }}</label>

        @include('crud::texts.' . $type)
    </fieldset>
@endforeach