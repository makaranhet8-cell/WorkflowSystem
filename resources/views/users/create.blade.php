{{-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create User - Workflow System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Workflow System</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a>
                <a class="nav-link" href="{{ route('logout') }}">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h1>Create User</h1>
        <form action="{{ route('users.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                @error('name') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                @error('email') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
                @error('password') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <select id="role" name="role" class="form-select" required>
                    <option value="user" {{ old('role') === 'user' ? 'selected' : '' }}>user</option>
                    <option value="team_leader" {{ old('role') === 'team_leader' ? 'selected' : '' }}>Team Leader</option>
                    <option value="hr_manager" {{ old('role') === 'hr_manager' ? 'selected' : '' }}>HR Manager</option>
                    <option value="ceo" {{ old('role') === 'ceo' ? 'selected' : '' }}>CEO</option>
                    <option value="cfo" {{ old('role') === 'cfo' ? 'selected' : '' }}>CFO</option>
                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin Department</option>
                </select>
                @error('role') <div class="text-danger">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Select Department</label>
                <select name="department_id" class="form-control" required>
                    <option value=""> Choose Department </option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="footer d-flex justify-content-between">
                <a href="{{ route('dashboard') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Create User</button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> --}}
