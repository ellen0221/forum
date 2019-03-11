@if( count($errors) > 0 )
    <div class="alert alert-danger">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
{{--Blade模板提供的更便利的方法--}}
{{--@if (count($errors) > 0)--}}
    {{--{{ count($errors) }}--}}
{{--@endif--}}