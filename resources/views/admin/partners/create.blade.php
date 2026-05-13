@extends('layouts.admin')

@section('content')

<div class="container">
    <h1>Tambah Partner</h1>

    <form action="/admin/partners/store" method="POST">
        @csrf

        <div class="mb-3">
            <label>Nama Partner</label>
            <input type="text" name="name" class="form-control">
        </div>

        <div class="mb-3">
            <label>Logo URL</label>

            <select name="logo_url" class="form-control">
                <option value="https://placehold.co/200x200">
                    Logo 1
                </option>

                <option value="https://placehold.co/200x200/orange/white">
                    Logo 2
                </option>

                <option value="https://placehold.co/200x200/black/white">
                    Logo 3
                </option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">
            Simpan
        </button>
    </form>
</div>

@endsection