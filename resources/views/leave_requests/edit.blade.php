<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  </head>
  <body>
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

        <form action="{{ route('leave-requests.update', $leaveRequest->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label class="form-label">User Name:</label>
        <input type="text" name="user_name" value="{{ $leaveRequest->user->name }}" class="form-control">
    </div>

    <div class="mb-3">
        <label class="form-label">Start Date:</label>
        <input type="date"
               name="start_date"
               value="{{ \Carbon\Carbon::parse($leaveRequest->start_date)->format('Y-m-d') }}"
               class="form-control">
    </div>

    <div class="mb-3">
        <label class="form-label">End Date:</label>
        <input type="date"
               name="end_date"
               value="{{ \Carbon\Carbon::parse($leaveRequest->end_date)->format('Y-m-d') }}"
               class="form-control">
    </div>

    <div class="mb-3">
        <label class="form-label">Reason:</label>
        <textarea name="reason" class="form-control" rows="3">{{ $leaveRequest->reason }}</textarea>
    </div>

    <button type="submit" class="btn btn-primary btn-update shadow-sm">
        Update User
    </button>
    <a href="{{ route('leave-requests.index') }}" class="link-cancel">Cancel</a>
</form>
    </div>
</div>

</body>
</html>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  </body>
</html>
