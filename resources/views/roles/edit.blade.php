<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Role</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; padding-top: 50px; }
        .form-card {
            background: #fff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            max-width: 800px;
            margin: 0 auto;
        }
        .btn-dark-custom { background-color: #1a1d2b; border: none; padding: 8px 20px; }
        .btn-dark-custom:hover { background-color: #2d324a; }
        label { font-weight: 500; margin-bottom: 8px; color: #333; }
        .permission-item { margin-bottom: 10px; }
    </style>
</head>
<body>

<div class="container">
    <div class="form-card">
        <div class="header d-flex justify-content-between align-items-center mb-4">
            <h2>Edit Role: {{ $role->name }}</h2>
            <a href="{{ route('roles.index') }}" class="btn btn-dark btn-dark-custom">Back</a>
        </div>

        <form action="{{ route('roles.update', $role->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="roleName" class="form-label">Role Name</label>
                <input value="{{ old('name', $role->name) }}"
                       type="text"
                       name="name"
                       class="form-control @error('name') is-invalid @enderror"
                       id="roleName"
                       placeholder="Enter Role Name"
                       style="height: 45px;">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label class="form-label d-block">Assign Permissions</label>
                <div class="row">
                    @if ($permissions->isNotEmpty())
                        @foreach ($permissions as $permission)
                            <div class="col-md-4 permission-item">
                                <div class="form-check">
                                    <input {{ collect($hasPermissions)->contains($permission->name) ? 'checked' : '' }}
                                           class="form-check-input"
                                           type="checkbox"
                                           id="permission-{{ $permission->id }}"
                                           name="permission[]"
                                           value="{{ $permission->name }}">
                                    <label class="form-check-label" for="permission-{{ $permission->id }}">
                                        {{ $permission->name }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted ps-3">No permissions available.</p>
                    @endif
                </div>
            </div>

            <div class="pt-3 border-top">
                <button type="submit" class="btn btn-dark btn-dark-custom">Update Role</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
