<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mission Request Details - Workflow System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/show.css') }}" rel="stylesheet">
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

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h2 class="card-title">Mission Request Details</h2>
                        <p class="text-muted">Status: <span class="badge bg-{{ $missionRequest->status === 'approved' ? 'success' : ($missionRequest->status === 'rejected' ? 'danger' : 'warning') }}">{{ ucfirst($missionRequest->status) }}</span></p>

                        <dl class="row">
                            <dt class="col-sm-4">Requested by</dt>
                            <dd class="col-sm-8">{{ $missionRequest->user->name }}</dd>

                            <dt class="col-sm-4">Destination</dt>
                            <dd class="col-sm-8">{{ $missionRequest->destination }}</dd>

                            <dt class="col-sm-4">Purpose</dt>
                            <dd class="col-sm-8">{{ $missionRequest->purpose }}</dd>

                            <dt class="col-sm-4">Start Date</dt>
                            <dd class="col-sm-8">{{ $missionRequest->start_date->format('Y-m-d') }}</dd>

                            <dt class="col-sm-4">End Date</dt>
                            <dd class="col-sm-8">{{ $missionRequest->end_date->format('Y-m-d') }}</dd>

                            <dt class="col-sm-4">Department</dt>
                            <dd class="col-sm-8">{{ $missionRequest->user->departments->first()->name ?? 'N/A' }}</dd>

                            <dt class="col-sm-4">Submitted</dt>
                            <dd class="col-sm-8">{{ $missionRequest->created_at->format('Y-m-d H:i') }}</dd>
                        </dl>
                        <div class="footer-show d-flex justify-content-between">
                            <a href="{{ route('mission-requests.index') }}" class="btn btn-info text-white">Back to Dashboard</a>
                            @if(Auth::user()->isApprover() && $missionRequest->status === 'pending')
                                <div class="mt-3">
                                    <form action="{{ route('approver.mission.approve', $missionRequest) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success">Approve</button>
                                    </form>
                                    <form action="{{ route('approver.mission.reject', $missionRequest) }}" method="POST" class="d-inline ms-2">
                                        @csrf
                                        <button type="submit" class="btn btn-danger">Reject</button>
                                    </form>
                                </div>
                            @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
