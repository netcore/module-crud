@foreach($row->translations as $translation)
    <div>
        <strong>{{ strtoupper($translation->locale) }}:</strong> {{ $translation->$field }}
    </div>
@endforeach