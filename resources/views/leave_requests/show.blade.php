<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Request Details - Workflow System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
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
                        <h2 class="card-title">Leave Request Details</h2>
                        <p class="text-muted">Status: <span class="badge bg-{{ $leaveRequest->status === 'approved' ? 'success' : ($leaveRequest->status === 'rejected' ? 'danger' : 'warning') }}">{{ ucfirst($leaveRequest->status) }}</span></p>

                        <dl class="row">
                            <dt class="col-sm-4">Requested by</dt>
                            <dd class="col-sm-8">{{ $leaveRequest->user->name }}</dd>

                            <dt class="col-sm-4">Start Date</dt>
                            <dd class="col-sm-8">{{ $leaveRequest->start_date->format('d-m-Y') }}</dd>

                            <dt class="col-sm-4">End Date</dt>
                            <dd class="col-sm-8">{{ $leaveRequest->end_date->format('d-m-Y') }}</dd>

                            <dt class="col-sm-4">Reason</dt>
                            <dd class="col-sm-8">{{ $leaveRequest->reason }}</dd>

                            <dt class="col-sm-4">Department</dt>
                            <dd class="col-sm-8">{{ $leaveRequest->user->departments->first()->name ?? 'N/A' }}</dd>

                            <dt class="col-sm-4">Submitted</dt>
                            <dd class="col-sm-8">{{ $leaveRequest->created_at->format('d-m-Y H:i') }}</dd>
                        </dl>
                        <div class="footer-show d-flex justify-content-between">
                            <a href="{{ route('leave-requests.index') }}" class="btn btn-info text-white"><i class="fa-solid fa-arrow-left"></i> Back</a>
                            @if(Auth::user()->isApprover() && $leaveRequest->status === 'pending')
                                <div class="mt-3">
                                    <form action="{{ route('approver.leave.approve', $leaveRequest) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success">Approve</button>
                                    </form>
                                    <form action="{{ route('approver.leave.reject', $leaveRequest) }}" method="POST" class="d-inline ms-2">
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
