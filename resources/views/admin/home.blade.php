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

            <div class="row" style="padding-bottom: 30px;">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">Товаров</div>

                        <div class="card-body text-center">
                            <span style="font-size: 28px;">{{ \App\Models\Products::count() }}</span>
                             <a style="position: relative; top: -5px; left: 30px;" href="{{ route('getGoogleSheets') }}" class="btn btn-success btn-sm">Загрузить товары с Google Sheets</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">Заказов</div>

                        <div class="card-body text-center">
                            <span style="font-size: 28px;">{{ \App\Models\Orders::count() }}</span>
                        </div>
                    </div>
                </div>

            </div>

            <div class="card">
                <div class="card-header">Товары</div>

                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Photo</th>
                            <th scope="col">Name</th>
                            <th scope="col">Qty</th>
                            <th scope="col">Availability</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($products as $product)
                            <tr>
                                <th scope="row"> {{ $product->id }}</th>
                                <td><img src="{{ $product->img }}" style="width: 50px;"></td>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->qty }}</td>
                                <td>{{ $product->availability }}</td>
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
