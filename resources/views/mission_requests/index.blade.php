<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Mission Requests</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    </head>
  <body>
    <style>
        .card-header { background-color: #212529; border-bottom: 1px solid #444; }
        .table thead th { background-color: #f8f9fa; color: #333; border-bottom: 2px solid #dee2e6; }
        .table tbody td { vertical-align: middle; color: #444; background-color: #fff !important; }
        .badge-status { font-weight: bold; text-transform: uppercase; padding: 5px 10px; border-radius: 12px; }
        .dept-badge { background-color: #f1f3f5; color: #495057; border: 1px solid #ced4da; font-weight: 400; }
    </style>


    <div class="container mt-5">
        <div class="d-flex justify-content-between mt-3 mb-4">
            <h2 class="text-dark"><i class="fa-solid fa-file-invoice me-2"></i>Mission Request Management</h2>

            <a class="btn btn-secondary" href="{{ route('dashboard') }}"><i class="fa-solid fa-arrow-left"></i>Back Dashboard</a>
        </div>
        <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center py-3 text-white" style="background-color: #212529;">
            <h6 class="mb-0 text-uppercase fw-bold">List of Mission Requests</h6>
            <span class="badge rounded-pill bg-primary px-3">{{ $missionRequests->count() }} Requests</span>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Duration (Start - End)</th>
                            <th>Purpose</th>
                            <th>Status</th>
                            <th>Department</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($missionRequests as $request)
                        <tr>
                            <td class="text-muted">{{ $loop->iteration }}</td>
                            <td class="fw-bold">{{ $request->user->name }}</td>
                            <td>
                                <div class="small">
                                    <i class="fa-regular fa-calendar-check text-primary"></i> {{ \Carbon\Carbon::parse($request->start_date)->format('d-m-Y') }}<br>
                                    <i class="fa-regular fa-calendar-xmark text-danger"></i> {{ \Carbon\Carbon::parse($request->end_date)->format('d-m-Y') }}
                                </div>
                            </td>
                            <td>{{ $request->purpose }}</td>
                            <td>
                                @php
                                    $statusClass = [
                                        'approved' => 'success',
                                        'rejected' => 'danger',
                                        'pending_tl' => 'warning text-dark',
                                        'pending_hr' => 'info text-white'
                                    ][$request->status] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $statusClass }} badge-status" style="font-size: 0.75rem;">
                                    {{ str_replace('_', ' ', strtoupper($request->status)) }}
                                </span>
                            </td>
                            <td>
                                @foreach($request->user->departments as $dept)
                                    <span class="badge dept-badge text-dark">{{ $dept->name }}</span>
                                @endforeach
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    @if ($request->status === 'pending_tl')
                                        <a href="{{ route('mission-requests.edit', $request->id) }}" class="btn btn-outline-info btn-sm mx-1">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                    @endif

                                    <a href="{{ route('mission-requests.show', $request->id) }}" class="btn btn-outline-success btn-sm mx-1">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    @can('delete requests')

                                @if(Auth::user()->hasAnyRole(['system_admin', 'admin']) || $request->status === 'pending_tl')
                                    <form action="{{ route('mission-requests.destroy', $request->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm mx-1" onclick="return confirm('Are you sure you want to delete this mission?')">
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
            </div>
        </div>
    </div>
</div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  </body>
</html>
