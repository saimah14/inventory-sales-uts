@if($errors->any())
    <div class="alert">
        <ul style="margin:0;padding-left:18px;">
            @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
@endif
