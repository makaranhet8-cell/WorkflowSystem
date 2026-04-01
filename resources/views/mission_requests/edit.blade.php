<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Mission Request</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link href="{{ asset('css/mission_edit.css') }}" rel="stylesheet">
</head>
<body>

<div class="main-container">
    <div class="form-card">
        <h2>Edit Mission Request</h2>

        <form action="{{ route('mission-requests.update', $missionRequest->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">User Name:</label>
                <input type="text" name="user_name" value="{{ $missionRequest->user->name }}" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Destination:</label>
                <input type="text" name="destination" value="{{ $missionRequest->destination }}" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Purpose:</label>
                <textarea name="purpose" class="form-control" rows="2" required>{{ $missionRequest->purpose }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Start Date:</label>
                <input type="datetime-local" name="start_date"
                       value="{{ \Carbon\Carbon::parse($missionRequest->start_date)->format('Y-m-d\TH:i') }}"
                       class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">End Date:</label>
                <input type="datetime-local" name="end_date"
                       value="{{ \Carbon\Carbon::parse($missionRequest->end_date)->format('Y-m-d\TH:i') }}"
                       class="form-control" required>
            </div>

            <button type="submit" class="btn-update shadow-sm">Update Mission</button>
            <a href="{{ route('mission-requests.index') }}" class="link-cancel">Cancel</a>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
