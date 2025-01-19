@extends('layouts.app')

@section('content')
    @if (Session::has('message'))
        <div class="alert alert-info">{{ Session::get('message') }}</div>
    @endif
    <label class="control-label">
        Status
    </label>
    <select onchange="location.href='{{ route('pet.list') }}?status='+this.value" class="form-control">
        @foreach(array_filter(\App\Enums\PetStatus::cases(), fn($case) => $case->name !== 'UNDEFINED') as $option)
            <option @if(request()->status == $option->value) selected @endif>{{ $option }}</option>
        @endforeach
    </select>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nazwa</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($pets as $pet)
                <tr>
                    <td>
                        {{ $pet->id }}
                    </td>
                    <td>
                        {{ $pet->name ?? 'brak' }}
                    </td>
                    <td class="d-flex g-2">
                        <a class="btn btn-warning" href="{{ route('pet.show', ['petId' => $pet->id]) }}">Edycja</a>
                        <form action="{{ route('pet.destroy', ['petId' => $pet->id]) }}" method="POST">
                            @method('delete')
                            @csrf
                            <input type="hidden" name="petId">
                            <input type="submit" class="btn btn-warning" value="Usuń">
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="w-100 text-center">
        {{ $pets->links() }}
    </div>

@endsection
