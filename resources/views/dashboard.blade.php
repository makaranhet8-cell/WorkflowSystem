<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Workflow System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
</head>
<body class="bg-dark text-white">
    <div class="container-fluid ">
        <div class="d-flex">

            <!-- Sidebar -->
            <div class="bg-black-subtle p-3 vh-100" style="width: 250px;">
                <h4 class="text-info mb-4">Dashboard</h4>

                <ul class="nav flex-column ">
                    <li class="nav-item mb-2">
                        <a href="{{ route('dashboard') }}" class="nav-link text-white ">Home</a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="{{ route('leave-requests.index') }}" class="nav-link text-white">List Leave Requests</a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="{{ route('mission-requests.index') }}" class="nav-link text-white">List Mission Requests</a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="{{ route('approver.dashboard') }}" class="nav-link text-white">Approvers</a>
                    </li>

                </ul>
            </div>

            <!-- Main Content -->
            <div class="container-fluid m-3">


            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Welcome to <span class="text-primary">{{ Auth::user()->name }}</span></h2>

                <div class="dropdown">
                    <div class="d-flex align-items-center" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=random&color=fff"
                            alt="Profile"
                            class="rounded-circle  border-2 border-secondary me-2"
                            style="width: 45px; height: 45px; object-fit: cover;">
                        <div class="text-start">
                            <span class="d-block fw-bold text-white">{{ Auth::user()->name }}</span>
                            <small class="text-muted d-block" style="font-size: 0.75rem;">{{ Auth::user()->role ?? 'User' }}</small>
                        </div>
                        <i class="ms-2 text-white" style="font-size: 0.8rem;"><i class="fa-solid fa-arrow-down"></i></i>
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark">
                        <li><a class="dropdown-item" href="{{ route('logout') }}" class="btn btn-danger">Logout</a></li>
                    </ul>
                </div>
            </div>
            <div class="row mt-4">
                <div class="row mt-4">
                    <div class="col-sm-3">
                        <div class="card bg-success text-white p-3">
                            <h6>Users</h6>
                        <h3>{{ count($allUsers) }}</h3>
                            <span class="badge bg-secondary">+12%</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-secondary text-white p-3">
                            <h6>Leave Requests</h6>
                            <h3>{{ $leaveRequests->count() }}</h3>
                            <span class="badge bg-danger">-5%</span>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card bg-warning text-white p-3">
                            <h6>Mission Requests</h6>
                            <h3>{{ $missionRequests->count() }}</h3>
                            <span class="badge bg-success">+8%</span>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card bg-info text-white p-3">
                            <h6>Status</h6>
                            <h3>99.9%</h3>
                            <span class="badge bg-primary">Stable</span>
                        </div>
                    </div>

                </div>
                <div class="d-flex justify-content-end gap-2 mt-3">
                    <!-- Create Leave -->
                    <a href="{{ route('leave-requests.create') }}"
                    class="btn btn-outline-primary">
                        <span><i class="fa-solid fa-user-plus"></i> </span> Leave Request
                    </a>
                    <!-- Create Mission -->
                    <a href="{{ route('mission-requests.create') }}"
                    class="btn btn-outline-info">
                        <span><i class="fa-solid fa-user-plus"></i> </span> Mission Request
                    </a>
                    @if(Auth::user()->role === 'admin' || Auth::user()->role === 'system_admin')
                        <a href="{{ route('admin.users.create') }}"
                        class="btn btn-outline-success">
                            <span><i class="fa-solid fa-user-plus"></i> </span> Create User
                        </a>
                    @endif
                </div>


                <!-- Recent Users Table -->
                @if (auth()->user()->role === 'admin' || auth()->user()->role === 'system_admin')
                <div class="card mt-5 bg-dark text-white">
                    <div class="card-header text-success">
                        <h5>List Users</h5>
                    </div>

                    <div class="card-body">
                        <table class="table table-dark table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Departments</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($allUsers as $index => $user)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->role }}</td>

                                    <td>
                                        @if($user->departments->isEmpty())
                                            <span class="badge bg-secondary">No Dept</span>
                                        @else
                                            @foreach($user->departments as $dept)
                                                <span class="badge bg-success">{{ $dept->name }}</span>
                                            @endforeach
                                        @endif

                                    </td>

                                    <td>
                                        <div class="d-flex">
                                            <a href="{{ route('admin.users.department.edit', $user->id) }}" class="btn btn-info me-2 text-light">
                                               <i class="fa-solid fa-share-from-square"></i> Allocate

                                            </a>
                                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-success me-2">
                                            <i class="fa-solid fa-pen-to-square"></i> Edit
                                        </a>

                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">
                                                <i class="fa-solid fa-trash text-white"></i> Delete
                                            </button>
                                        </form>
                                        </div>
                                    </td>


                                </tr>

                                @endforeach

                            </tbody>

                        </table>
                    </div>
                </div>

                @endif
            </div>
        </div>


</div>


   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

