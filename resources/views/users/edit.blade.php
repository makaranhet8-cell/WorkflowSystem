{{-- <!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link href="{{ asset('css/edit.css') }}" rel="stylesheet">
</head>
<body>

<div class="main-container">
    <div class="form-card">
        <h2>Edit User:</h2>

        <form action="{{ route('users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}"
                    class="form-control @error('name') is-invalid @enderror">
                @error('name') <div class="error-msg">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                    class="form-control @error('email') is-invalid @enderror">
                @error('email') <div class="error-msg">{{ $message }}</div> @enderror
            </div>

            <div class="mb-4">
                <label class="form-label">Department</label>
                <select name="department" class="form-select">
                    <option value="IT Department" {{ $user->department == 'IT Department' ? 'selected' : '' }}>IT Department</option>
                    <option value="Sales Department" {{ $user->department == 'Sales Department' ? 'selected' : '' }}>Sales Department</option>
                </select>
            </div>

            <button type="submit" class="btn-update shadow-sm">
                Update User
            </button>
            <a href="{{ route('dashboard') }}" class="link-cancel">Cancel</a>
        </form>
    </div>
</div>

</body>
</html> --}}
