@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">@if (isset($title)) {{ $title }} @endif</div>

                <div class="panel-body">
                    @include('layouts.messages')

                    @if (!is_null($fields))
                        @if (isset($title))
                            <h2>{{ $title }}</h2>
                        @endif

                        @if ($dataset->id)
                            {!! Form::model($dataset, ['url' => $submitRoute, 'class' => 'form-horizontal']) !!}
                        @else
                            {!! Form::open(['url' => $submitRoute, 'class' => 'form-horizontal']) !!}
                        @endif

                        @php($fields_class = ['class' => 'form-control'])

                        @foreach ($fields as $field)
                            <div class="form-group{{ isset($errors) && $errors->has($field['id']) ? ' has-error' : '' }}">
                                {{ Form::label($field['id'], $field['title'], ['class' => 'col-md-4 control-label']) }}

                                @php(isset($field['optional']) ? $fields_attributes = $fields_class + $field['optional'] : $fields_attributes = $fields_class)
                                @php(!isset($field['optional']['class']) ?: $fields_attributes['class'] .= ' '. $field['optional']['class'])

                                {{-- Autofocus on first field in form --}}
                                @if ($loop->first)
                                    @php($fields_attributes['autofocus'] = 'autofocus')
                                @elseif ($loop->index === 1)
                                    @unset($fields_attributes['autofocus'])
                                @endif

                                <div class="col-md-6">
                                    @php(isset($field['type']) ? $type = $field['type'] : $type = 'text')

                                    @if ($type === 'select')
                                        {{ Form::$type($field['id'], $field['selectable'], $field['value']($dataset), $fields_attributes) }}
                                    @else
                                        {{ Form::$type($field['id'], $field['value']($dataset), $fields_attributes) }}
                                    @endif

                                    @if (isset($errors) && $errors->has($field['id']))
                                        <span class="help-block">
                                        <strong>{{ $errors->first($field['id']) }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">@lang('general.send')</button>
                        </div>

                        {!! Form::close() !!}
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
