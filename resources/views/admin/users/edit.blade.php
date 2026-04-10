<!doctype html>
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
            <form action="{{ route('admin.users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}"
                    class="form-control @error('name') is-invalid @enderror">
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                    class="form-control @error('email') is-invalid @enderror">
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <label for="">Image</label>
                <input type="file" name="profile_image" class="form-control" id="image" name="image" value="{{ old('image', $user->image) }}" >
            </div>
            <button type="submit" class="btn btn-primary shadow-sm w-100">
                Update User
            </button>

            <div class="text-center mt-3">
                <a href="{{ route('dashboard') }}" class="link-secondary text-decoration-none">Cancel</a>
            </div>
        </form>
     </div>
</div>

</body>
</html>
