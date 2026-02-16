@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <h3 style="margin-bottom: 15px">Atur Jadwal untuk {{ $user->cname }}</h3>

        <form action="{{ route('schedule.assign') }}" method="POST">
            @csrf
            <input type="hidden" name="nuserid" value="{{ $user->nid }}">

            <table class="table table-bordered">
                <thead class="table-secondary">
                    <tr>
                        <th>Tanggal</th>
                        <th>Shift</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($period as $date)
                        @php $d = $date->format('Y-m-d'); @endphp
                        <tr>
                            <td>{{ $date->format('d/m/Y') }}</td>
                            <td>
                                <select name="dates[{{ $d }}]" class="form-select">
                                    <option value="">-- none --</option>
                                    @foreach ($masters as $m)
                                        <option value="{{ $m->nid }}"
                                            @if (isset($existingSchedules[$d]) && $existingSchedules[$d] == $m->nid) selected @endif>
                                            {{ $m->cname }}
                                            ({{ substr($m->dstart, 0, 5) }} - {{ substr($m->dend, 0, 5) }}
                                            @if ($m->dstart2 && $m->dend2)
                                                | {{ substr($m->dstart2, 0, 5) }} - {{ substr($m->dend2, 0, 5) }}
                                            @endif)
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <button type="submit" class="btn btn-success">Simpan Jadwal</button>
            <a href="{{ route('schedule.index') }}" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
@endsection
