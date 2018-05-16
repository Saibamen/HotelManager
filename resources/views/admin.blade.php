@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">@if (isset($title)) {{ $title }} @endif</div>

                <div class="panel-body">
                    @include('layouts.messages')

                    <h4>{{ trans('general.delete') }}</h4>

                    <div class="btn-group btn-group-justified">
                        {{ Html::link('#', trans('general.rooms'), ['class' => 'btn btn-danger', 'role' => 'button', 'data-toggle' => 'modal', 'data-target' => '#delete-modal', 'data-href' => url('admin/deleteRooms'), 'data-message' => trans('general.delete_associated_reservations')]) }}

                        {{ Html::link('#', trans('general.guests'), ['class' => 'btn btn-danger', 'role' => 'button', 'data-toggle' => 'modal', 'data-target' => '#delete-modal', 'data-href' => url('admin/deleteGuests'), 'data-message' => trans('general.delete_associated_reservations')]) }}

                        {{ Html::link('#', trans('general.reservations'), ['class' => 'btn btn-danger', 'role' => 'button', 'data-toggle' => 'modal', 'data-target' => '#delete-modal', 'data-href' => url('admin/deleteReservations')]) }}
                    </div>

                    <br /><strong>{{ trans('general.warning') }}</strong> {{ trans('general.remember_backup') }}
                </div>
            </div>
        </div>
    </div>
</div>

@include('deletemodal')

@endsection
