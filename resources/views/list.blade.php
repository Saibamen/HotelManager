@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ $title or null }}</div>

                <div class="card-body">
                    @include('layouts.messages')

                    @if (!is_null($dataset))
                        @if (isset($title))
                            <h2>{{ $title }}</h2>
                        @endif

                        @if ($dataset->total() > 0)
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr class="table-active">
                                        @foreach ($columns as $column)
                                            <th>{{ $column['title'] }}</th>
                                        @endforeach

                                        <th style="width:130px;">@lang('general.actions')</th>
                                    </tr>
                                </thead>

                                <tbody>
                                @foreach ($dataset as $data)
                                    <tr>
                                        @foreach ($columns as $column)
                                            <td>{!! $column['value']($data) !!}</td>
                                        @endforeach

                                        {{-- Akcje --}}
                                        <td>
                                            @if (!isset($routeChooseName))
                                                @if (!isset($disableEdit) || !$disableEdit)
                                                    {{ Html::link(route($routeName.'.editform', $data->id), trans('general.edit'), ['class' => 'btn btn-sm btn-primary']) }}
                                                @endif

                                                {{ Form::button(trans('general.delete'), ['class' => 'btn btn-sm btn-danger', 'data-toggle' => 'modal', 'data-target' => '#delete-modal', 'data-id' => $data->id, 'data-message' => isset($deleteMessage) ? $deleteMessage : '' ]) }}
                                            @else
                                                @php($routeParams = [$data->id])
                                                @php(!isset($additionalRouteParams) ?: $routeParams = array_merge((array) $additionalRouteParams, $routeParams))
                                                {{ Html::link(route($routeChooseName, $routeParams), trans('general.select'), ['class' => 'btn btn-sm btn-primary']) }}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @endif

                        <div class="text-center">
                            @if ($dataset->total() > $dataset->perPage())
                                {{ $dataset->links() }}
                                <br>
                            @endif

                            <a href="{{ route($routeName.'.addform') }}" class="btn btn-success" role="button">
                                <i class="fa fa-plus"></i> @lang('general.add')
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@if (!isset($routeChooseName) && $dataset->total() > 0)
    @include('deletemodal')
@endif

@endsection
