<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approver Dashboard - Workflow System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Workflow System - Approver</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link btn btn-outline-warning" href="{{ route('dashboard') }}">Back Dashboard</a>
                <a class="nav-link btn btn-outline-danger" href="{{ route('logout') }}">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <h1 class="mb-4 text-primary">Pending Requests for Approval</h1>

        <div class="row">
            <div class="col-lg-6">
                <div class="card bg-dark">
                    <div class="card-header">
                        <h5 class="mb-0 text-light">Leave Requests</h5>
                    </div>
                    <div class="card-body bg-dark">
                        @if($pendingLeaveRequests->isEmpty())
                            <p class="text-muted ">No pending leave requests.</p>
                        @else
                            <div class="list-group list-group-flush">
                                @foreach($pendingLeaveRequests as $request)
                                    <div class="list-group-item bg-dark">
                                        <div class="d-flex justify-content-between align-items-center bg-dark">
                                            <div class="text-light">
                                                <strong>{{ $request->user->name }}</strong><br>
                                                <small>{{ $request->start_date->format('d-m-Y') }} to {{ $request->end_date->format('d-m-Y') }}</small>
                                            </div>
                                            <div>
                                                <td>
                                                    <div class="d-flex gap-1">
                                                        {{-- សម្រាប់ Team Leader (ទាំង IT និង Sales) --}}
                                                        @if(Auth::user()->hasRole('team_leader') && $request->status == 'pending_tl')
                                                            <form action="{{ route('approver.leave.approve', $request->id) }}" method="POST">
                                                                @csrf
                                                                <button type="submit" class="btn btn-sm btn-success">Approve (TL)</button>
                                                            </form>
                                                            <form action="{{ route('approver.leave.reject', $request->id) }}" method="POST">
                                                                @csrf
                                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('បដិសេធសំណើនេះ?')">Reject</button>
                                                            </form>
                                                        @endif

                                                        {{-- សម្រាប់ CFO (បង្ហាញតែសំណើរបស់ Sales ប៉ុណ្ណោះ) --}}
                                                        @if(Auth::user()->hasRole('cfo') && $request->status == 'pending_cfo')
                                                            <form action="{{ route('approver.leave.approve', $request->id) }}" method="POST">
                                                                @csrf
                                                                <button type="submit" class="btn btn-sm btn-success">Approve (CFO)</button>
                                                            </form>
                                                            <form action="{{ route('approver.leave.reject', $request->id) }}" method="POST">
                                                                @csrf
                                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('បដិសេធសំណើនេះ?')">Reject</button>
                                                            </form>
                                                        @endif

                                                        {{-- សម្រាប់ HR Manager (ដំណាក់កាលចុងក្រោយ) --}}
                                                        @if(Auth::user()->hasRole('hr_manager') && $request->status == 'pending_hr')
                                                            <form action="{{ route('approver.leave.approve', $request->id) }}" method="POST">
                                                                @csrf
                                                                <button type="submit" class="btn btn-sm btn-success">Approve (HR)</button>
                                                            </form>
                                                            <form action="{{ route('approver.leave.reject', $request->id) }}" method="POST">
                                                                @csrf
                                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('បដិសេធសំណើនេះ?')">Reject</button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </td>

                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card bg-dark">
                    <div class="card-header">
                        <h5 class="mb-0 text-light">Mission Requests</h5>
                    </div>
                    <div class="card-body bg-dark">
                        @if($pendingMissionRequests->isEmpty())
                            <p class="text-muted">No pending mission requests.</p>
                        @else
                            <div class="list-group list-group-flush">
                                @foreach($pendingMissionRequests as $request)
                                    <div class="list-group-item bg-dark">
                                        <div class="d-flex justify-content-between align-items-center bg-dark">
                                            <div class="text-light">
                                                <strong>{{ $request->user->name }}</strong><br>
                                                <small>{{ $request->destination }} - {{ $request->start_date->format('Y-m-d') }}</small>
                                            </div>
                                            <div>
                                                <div class="d-flex gap-1">
                                                    {{-- Team Leader --}}
                                                    @if(Auth::user()->hasRole('team_leader') && $request->status == 'pending_tl')
                                                        <form action="{{ route('approver.mission.approve', $request->id) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-success">Approve (TL)</button>
                                                        </form>
                                                        <form action="{{ route('approver.mission.reject', $request->id) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('បដិសេធសំណើនេះ?')">Reject</button>
                                                        </form>
                                                    @endif

                                                    {{-- CFO (សម្រាប់តែ Sales Department) --}}
                                                    @if(Auth::user()->hasRole('cfo') && $request->status == 'pending_cfo')
                                                        <form action="{{ route('approver.mission.approve', $request->id) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-success">Approve (CFO)</button>
                                                        </form>
                                                        <form action="{{ route('approver.mission.reject', $request->id) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('បដិសេធសំណើនេះ?')">Reject</button>
                                                        </form>
                                                    @endif

                                                    {{-- HR Manager --}}
                                                    @if(Auth::user()->hasRole('hr_manager') && $request->status == 'pending_hr')
                                                        <form action="{{ route('approver.mission.approve', $request->id) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-success text-white">Approve (HR)</button>
                                                        </form>
                                                        <form action="{{ route('approver.mission.reject', $request->id) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('បដិសេធសំណើនេះ?')">Reject</button>
                                                        </form>
                                                    @endif

                                                    {{-- CEO (ដំណាក់កាលចុងក្រោយនៃបេសកកម្ម) --}}
                                                    @if(Auth::user()->hasRole('ceo') && $request->status == 'pending_ceo')
                                                        <form action="{{ route('approver.mission.approve', $request->id) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-success">Approve (CEO)</button>
                                                        </form>
                                                        <form action="{{ route('approver.mission.reject', $request->id) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('បដិសេធសំណើនេះ?')">Reject</button>
                                                        </form>
                                                    @endif
                                                </div>
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
