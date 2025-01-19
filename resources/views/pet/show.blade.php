@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-2">
            <h2>Informacje</h2>
            <div>
                <h5>
                    Nazwa
                </h5>
                <p>
                    {{ $pet->name }}
                </p>
            </div>
            <div>
                <h5>
                    Status
                </h5>
                <p>
                    {{ $pet->status->value }}
                </p>
            </div>
        </div>
        <div class="col-10">
            <h2>Edycja</h2>
            @if (Session::has('message'))
                <div class="alert alert-info">{{ Session::get('message') }}</div>
            @endif
            <form method="POST" action="{{ route('pet.update', ['petId' => $pet->id]) }}">
                @csrf
                @method('put')
                <div>
                    <label class="form-label">
                        Nazwa
                    </label>
                    <input class="form-control" name="name" value="{{ $pet->name }}">
                </div>
                <div>
                    <label class="form-label">
                        Status
                    </label>
                    <select name="status" class="form-control">
                        @foreach(array_filter(\App\Enums\PetStatus::cases(), fn($case) => $case->name !== 'UNDEFINED') as $option)
                            <option @if($pet->status->value == $option->value) selected @endif>{{ $option }}</option>
                        @endforeach
                    </select>
                </div>
                <input type="submit" class="btn btn-success">
            </form>
        </div>
    </div>
@endsection
