@extends('emails.layout')

@section('body')
<!-- content -->
<td valign="top" class="bodyContent" mc:edit="body_content">
    <h1 class="h1">{{$data['title']}}</h1>
    <p>{!! $data['body'] !!}</p>
    @if(isset($data['hasButton']))
    <a href="{{$data['buttonLink']}}" class="btn btn-primary">{{$data["buttonText"]}}</a>
    @endif
    @if(isset($data['hint']))
    <p>{!! $data['hint'] !!}</p>

    @endif
</td>
@endsection
@section("cancel")
<p class="footer">If you have any questions or concerns, please contact our support team at info@weworkperhour.com or visit our website <a href="https://www.weworkperhour.com">www.weworkperhour.com</a>.</p>
@endsection