@extends('layouts.app')

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if (Session::has('message'))
        <div class="alert alert-info">{{ Session::get('message') }}</div>
    @endif
    <form method="POST" action="{{ route('pet.store') }}">
        @csrf
        @method('post')
        <div>
            <div>
                <label class="form-label">
                    Nazwa
                </label>
                <input class="form-control" name="name">
            </div>
            <div>
                <label class="form-label">
                    Status
                </label>
                <select name="status" class="form-control">
                    @foreach(array_filter(\App\Enums\PetStatus::cases(), fn($case) => $case->name !== 'UNDEFINED') as $option)
                        <option @if(request()->status == $option->value) selected @endif>{{ $option }}</option>
                    @endforeach
                </select>
            </div>
            <input type="submit" class="btn btn-success mt-1">
        </div>
    </form>
@endsection
