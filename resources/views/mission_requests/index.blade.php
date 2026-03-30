<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mission Requests</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  </head>
  <body>
    <div class="d-flex justify-content-end mx-5 mt-3">
        <a class="btn btn-secondary" href="{{ route('dashboard') }}">Back Dashboard</a>
    </div>
    <div class="container p-5 mt-5">
        <div class="card bg-dark text-white">
            <div class="card-header d-flex justify-content-between">
                <h6>
                    {{ Auth::user()->isApproverOrDepartmentAdmin()
                        ? 'All Mission Requests'
                        : 'Your Mission Requests' }}
                </h6>
                <span class="badge bg-primary">{{ $missionRequests->count() }}</span>
            </div>

            <div class="card-body table-responsive">

                @if($missionRequests->isEmpty())
                    <p>No mission requests.</p>
                @else

                <table class="table table-dark table-sm table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Destination</th>
                            <th>Purpose</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Status</th>
                            <th>Department</th>
                            <th>Action</th>

                        </tr>
                    </thead>

                    <tbody>
                        @foreach($missionRequests as $request)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $request->user->name }}</td>
                            <td>{{ $request->destination }}</td>
                            <td>{{ $request->purpose }}</td>
                            <td>{{ \Carbon\Carbon::parse($request->start_date)->format('d-m-Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($request->end_date)->format('d-m-Y') }}</td>

                            <td>
                                <span class="badge bg-{{
                                    $request->status == 'approved' ? 'success' :
                                    ($request->status == 'rejected' ? 'danger' : 'warning')
                                }}">
                                    {{ $request->status }}
                                </span>
                            </td>

                            <td>
                                @foreach($request->user->departments as $dept)
                                    <span class="badge bg-success">{{ $dept->name }}</span>
                                @endforeach
                            </td>

                            <td>
                                <div class="d-flex">
                                    @if ($request->status === 'pending_tl')
                                        <a href="{{ route('mission-requests.edit', $request->id) }}" class="btn btn-sm btn-info me-2">
                                        Edit
                                    </a>
                                    @endif

                                    <a href="{{ route('mission-requests.show', $request->id) }}"
                                        class="btn btn-sm btn-success me-2">View</a>
                                    @if($request->status === 'pending_tl')
                                        <form action="{{ route('mission-requests.destroy', $request->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                    @endif
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  </body>
</html>
