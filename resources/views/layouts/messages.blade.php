<div id="alert-box" class="alert @if(Session::has('message')){{ Session::get('alert-class', 'alert-info') }}@endif" @if(!Session::has('message'))style="display: none;"@endif>
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    <div id="alert-message">{{ Session::get('message') }}</div>
</div>
{{ Session::forget('message') }}
{{ Session::forget('alert-class') }}
