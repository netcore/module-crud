<div class="media">
    <div class="media-left">
        @if(is_image($row->$field->url()))
            <a href="{{ extension_image($row->$field->url()) }}" target="_blank">
                <img src="{{ extension_image($row->$field->url()) }}" class="media-object" style="width:70px">
            </a>
        @else
            <img src="{{ extension_image($row->$field->url()) }}" class="media-object">
        @endif
    </div>
    <div class="media-body">
        <p>{{ basename($row->$field->url()) }}</p>
    </div>
</div>