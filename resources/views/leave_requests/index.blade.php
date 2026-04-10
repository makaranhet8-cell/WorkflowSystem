<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Leave Requests</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-dark"><i class="fa-solid fa-file-invoice me-2"></i>Leave Request Management</h2>
            <a class="btn btn-secondary shadow-sm" href="{{ route('dashboard') }}">
                <i class="fa-solid fa-arrow-left"></i> Back Dashboard
            </a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center py-3">
                <h6 class="mb-0 text-uppercase">List of Leave Requests</h6>
                <span class="badge rounded-pill bg-primary px-3">{{ $leaveRequests->count() }} Requests</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    @if($leaveRequests->isEmpty())
                        <div class="p-5 text-center">
                            <i class="fa-solid fa-folder-open fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No leave requests found.</p>
                        </div>
                    @else
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-secondary">
                            <tr>
                                <th class="ps-3">ID</th>
                                <th>User</th>
                                <th>Duration</th>
                                <th>Reason</th>
                                <th>Status</th>
                                <th>Department</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($leaveRequests as $request)
                            <tr>
                                <td class="ps-3 text-muted">{{ $loop->iteration }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $request->user->name }}</div>
                                </td>
                                <td>
                                    <div class="small">
                                        <i class="fa-regular fa-calendar-check text-primary me-1"></i>
                                        {{ \Carbon\Carbon::parse($request->start_date)->format('d-m-Y') }}
                                    </div>
                                    <div class="small text-muted">
                                        <i class="fa-regular fa-calendar-times text-danger me-1"></i>
                                        {{ \Carbon\Carbon::parse($request->end_date)->format('d-m-Y') }}
                                    </div>
                                </td>
                                <td><span class="text-truncate d-inline-block" style="max-width: 150px;">{{ $request->reason }}</span></td>
                                <td>
                                    @php
                                        $statusClass = [
                                            'approved' => 'success',
                                            'rejected' => 'danger',
                                            'pending_tl' => 'warning text-dark',
                                            'pending_hr' => 'info',
                                            'pending_cfo' => 'primary',
                                        ][$request->status] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $statusClass }} text-uppercase">
                                        {{ str_replace('_', ' ', $request->status) }}
                                    </span>
                                </td>
                                <td>
                                    @foreach($request->user->departments as $dept)
                                        <span class="badge border text-dark fw-normal bg-light">{{ $dept->name }}</span>
                                    @endforeach
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        @if (Auth::user()->can('edit requests'))
                                            @if($request->status === 'pending_tl')
                                                <a href="{{ route('leave-requests.edit', $request->id) }}" class="btn btn-outline-info btn-sm" title="Edit">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </a>
                                            @endif
                                        @endif
                                            <a href="{{ route('leave-requests.show', $request->id) }}" class="btn btn-outline-success btn-sm" title="View">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                       @can('delete requests')
                                            @if(Auth::user()->hasAnyRole(['system_admin', 'admin']) || $request->status === 'pending_tl')
                                                <form action="{{ route('leave-requests.destroy', $request->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm"
                                                            onclick="return confirm('តើអ្នកពិតជាចង់លុបសំណើនេះមែនទេ?')"
                                                            title="Delete">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
