@extends('layouts.hr')

@section('header', 'Restore Users')

@section('content')
    <div class="d-flex justify-content-end mb-4">
        <a href="{{ route('hr.users.index') }}" class="btn btn-secondary">Cancel</a>
    </div>

    <div class="card shadow-sm mx-auto" style="max-width: 600px;">
        <div class="card-body p-4">
            <div class="alert alert-info mb-4">
                <i class="bi bi-info-circle"></i>
                <strong>Restore from Backup</strong>
                <p class="mb-0 mt-2">Upload a previously exported users backup file (JSON format) to restore user data. Existing records with the same ID will be updated.</p>
            </div>

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('hr.users.restore.post') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="mb-4">
                    <label for="backup_file" class="form-label fw-bold">Select Backup File</label>
                    <input type="file" name="backup_file" id="backup_file" class="form-control @error('backup_file') is-invalid @enderror" accept=".json" required>
                    @error('backup_file') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    <small class="text-muted d-block mt-2">Accepted format: JSON files only (users_backup_*.json)</small>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-upload"></i> Restore Users
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
