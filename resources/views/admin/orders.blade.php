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
                <div class="card-header">Заказы</div>

                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Name</th>
                            <th scope="col">Phone</th>
                            <th scope="col">Date</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($orders as $order)
                            <tr>
                                <th scope="row"> {{ $order->id }}</th>
                                <td>{{ $order->name }}</td>
                                <td>{{ $order->phone }}</td>
                                <td>{{ $order->created_at }}</td>
                            </tr>
                        @endforeach
                        </tbody>

                    </table>

                </div>

            </div>
        </div>
    </div>
</div>
@endsection
