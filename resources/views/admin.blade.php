@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">@if (isset($title)) {{ $title }} @endif</div>

                <div class="card-body">
                    @include('layouts.messages')

                    <h4>{{ trans('general.options') }}</h4>

                    <div class="text-center">
                        {{ Html::link(route('admin.generate'), trans('navigation.generate_initial_state'), ['class' => 'btn btn-primary']) }}
                    </div>

                    <h4>{{ trans('general.delete') }}</h4>

                    <div class="btn-group d-flex" role="group">
                        {{ Html::link('#', trans('general.rooms'), ['class' => 'btn btn-danger', 'role' => 'button', 'data-toggle' => 'modal', 'data-target' => '#delete-modal', 'data-href' => url('admin/delete_rooms'), 'data-message' => trans('general.delete_associated_reservations')]) }}

                        {{ Html::link('#', trans('general.guests'), ['class' => 'btn btn-danger', 'role' => 'button', 'data-toggle' => 'modal', 'data-target' => '#delete-modal', 'data-href' => url('admin/delete_guests'), 'data-message' => trans('general.delete_associated_reservations')]) }}

                        {{ Html::link('#', trans('general.reservations'), ['class' => 'btn btn-danger', 'role' => 'button', 'data-toggle' => 'modal', 'data-target' => '#delete-modal', 'data-href' => url('admin/delete_reservations')]) }}
                    </div>

                    <br /><strong>{{ trans('general.warning') }}</strong> {{ trans('general.remember_backup') }}
                </div>
            </div>
        </div>
    </div>
</div>

@include('deletemodal')

@endsection
