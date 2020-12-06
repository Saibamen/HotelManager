<div class="modal fade" id="delete-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-url="{{ url('/') }}/{{ $routeName }}/delete/">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">@lang('general.delete')</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @lang('general.really_delete') <strong></strong>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">@lang('general.no')</button>
                <button id="delete-confirm" type="button" class="btn btn-danger">@lang('general.yes')</button>
            </div>
        </div>
    </div>
</div>

@section('js')
{!! Html::script('js/deletemodal.min.js') !!}
@endsection
