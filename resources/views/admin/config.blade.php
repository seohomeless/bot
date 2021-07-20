@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-3">
            <div class="card">
                @include('admin.menu')
            </div>
        </div>

        <div class="col-md-9">
            @if (\Session::has('success'))
                <div class="alert alert-success">
                    <ul>
                        <li>{!! \Session::get('success') !!}</li>
                    </ul>
                </div>
            @endif

            <div class="card">
                <div class="card-header">Настройки</div>

                <div class="card-body">
                   <form method="POST" action="{{route('configEdit')}}">
                       @csrf
                       <div class="form-group">
                          <textarea name="text"class="form-control">{{ $text }}</textarea>
                       </div>
                       <input type="submit" class="btn btn-success" value="Редактировать">
                   </form>

                </div>

            </div>
        </div>
    </div>
</div>
@endsection
