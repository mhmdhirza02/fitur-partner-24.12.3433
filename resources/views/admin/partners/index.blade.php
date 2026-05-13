@extends('layouts.admin')

@section('content')

<div class="container">

    <h1 class="text-2xl font-bold mb-4">
        Data Partners
    </h1>

    <a href="/admin/partners/create"
       class="inline-block bg-blue-600 text-white px-4 py-2 rounded mb-4">
        Tambah Partner
    </a>

    <div class="row">
        @foreach ($partners as $partner)

            <div class="col-md-4 mb-3">

                <div class="card p-3">

                    <img 
                        src="{{ $partner->logo_url }}" 
                        alt=""
                        class="img-fluid mb-2"
                    >

                    <h5>{{ $partner->name }}</h5>

                </div>

            </div>

        @endforeach
    </div>

</div>

@endsection