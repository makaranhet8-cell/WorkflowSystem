<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approver Dashboard - Workflow System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Workflow System - Approver</a>
            <div class="navbar-nav ms-auto gap-2">
                <a class="btn btn-outline-secondary btn-sm" href="{{ route('dashboard') }}"><i class="fa-solid fa-arrow-left"></i> Back to Dashboard</a>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-sm"><i class="fa-solid fa-right-from-bracket me-2"></i> Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container py-5">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <h1 class="mb-4 text-primary">Pending Requests for Approval</h1>

    <div class="row">

        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fa-solid fa-clock-rotate-left"></i> Leave Requests</h5>
                </div>
                <div class="card-body">
                    @if($pendingLeaveRequests->isEmpty())
                        <p class="text-muted text-center">No pending leave requests.</p>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($pendingLeaveRequests as $request)
                                <div class="list-group-item px-0 py-3">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1 text-dark">{{ $request->user->name }}</h6>
                                            <span class="badge bg-info mb-2">
                                                {{ $request->user->departments->pluck('name')->implode(', ') }}
                                            </span>
                                            <p class="small text-muted mb-1"><strong>Reason:</strong> {{ $request->reason ?? 'N/A' }}</p>
                                            <small class="text-secondary">
                                                <i class="fa-solid fa-calendar-days me-1"></i>
                                                {{ $request->start_date->format('d M Y') }} - {{ $request->end_date->format('d M Y') }}
                                            </small>
                                        </div>

                                        <div class="d-flex flex-column gap-1">
                                            @php

                                                $currentUser = Auth::user();
                                            @endphp


                                            @if($currentUser->hasRole('admin') ||
                                               ($currentUser->hasRole('team_leader') && $request->status == 'pending_tl') ||
                                               ($currentUser->hasRole('cfo') && $request->status == 'pending_cfo') ||
                                               ($currentUser->hasRole('hr_manager') && $request->status == 'pending_hr'))

                                                <form action="{{ route('approver.leave.approve', $request->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success w-100 mb-1">Approve</button>
                                                </form>

                                                <form action="{{ route('approver.leave.reject', $request->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-danger w-100" onclick="return confirm('Reject this request?')">Reject</button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Mission Requests Section --}}
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fa-solid fa-plane-departure"></i> Mission Requests</h5>
                </div>
                <div class="card-body">
                    @if($pendingMissionRequests->isEmpty())
                        <p class="text-muted text-center">No pending mission requests.</p>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($pendingMissionRequests as $request)
                                <div class="list-group-item px-0 py-3">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1 text-dark">{{ $request->user->name }}</h6>
                                            <span class="badge bg-secondary mb-2">
                                                {{ $request->user->departments->pluck('name')->implode(', ') }}
                                            </span>
                                            <p class="small text-muted mb-1"><strong>To:</strong> {{ $request->destination }}</p>
                                            <p class="small text-muted mb-1"><strong>Purpose:</strong> {{ $request->purpose }}</p>
                                            <small class="text-secondary">
                                                <i class="fa-solid fa-calendar-days me-1"></i>
                                                {{ $request->start_date->format('d M Y') }} - {{ $request->end_date->format('d M Y') }}
                                            </small>
                                        </div>

                                        <div class="d-flex flex-column gap-1">
                                            @php $currentUser = Auth::user(); @endphp

                                            @if($currentUser->hasRole('admin') ||
                                               ($currentUser->hasRole('team_leader') && $request->status == 'pending_tl') ||
                                               ($currentUser->hasRole('cfo') && $request->status == 'pending_cfo') ||
                                               ($currentUser->hasRole('hr_manager') && $request->status == 'pending_hr') ||
                                               ($currentUser->hasRole('ceo') && $request->status == 'pending_ceo'))

                                                <form action="{{ route('approver.mission.approve', $request->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success w-100 mb-1">Approve</button>
                                                </form>
                                                <form action="{{ route('approver.mission.reject', $request->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-danger w-100" onclick="return confirm('Reject this request?')">Reject</button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
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
