<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Workflow System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">

</head>
<body class="bg-dark text-white">
    <div class="container-fluid p-0">
        <div class="d-flex">

            <div class="bg-black-subtle p-3 vh-100 shadow" style="width: 260px; position: sticky; top: 0;">
                <h4 class="text-info mb-4 text-center"><i class="fa-solid fa-layer-group"></i> Workflow</h4>
                <hr class="text-secondary">
                <ul class="nav flex-column">
                    @if(Auth::user()->can('view home'))
                    <li class="nav-item mb-2">
                        <a href="{{ route('dashboard') }}" class="nav-link text-white {{ request()->is('dashboard') ? 'active-link' : '' }}">
                            <i class="fa-solid fa-house me-2"></i> Home
                        </a>
                    </li>
                    @endif
                    @if(Auth::user()->can('view leaverequests'))
                    <li class="nav-item mb-2">
                        <a href="{{ route('leave-requests.index') }}" class="nav-link text-white {{ request()->is('leave-requests*') ? 'active-link' : '' }}">
                            <i class="fa-solid fa-calendar-minus me-2"></i> List Leave
                        </a>
                    </li>
                    @endif
                    @if(Auth::user()->can('view missionrequests'))
                    <li class="nav-item mb-2">
                        <a href="{{ route('mission-requests.index') }}" class="nav-link text-white {{ request()->is('mission-requests*') ? 'active-link' : '' }}">
                            <i class="fa-solid fa-briefcase me-2"></i> List Mission
                        </a>
                    </li>
                    @endif
                    @hasanyrole('admin|approver|team_leader|hr_manager|ceo|cfo')
                        <li class="nav-item mb-2">
                            @if(Auth::user()->can('view approverequests'))
                            <a href="{{ route('approver.dashboard') }}" class="nav-link text-white">
                                <i class="fa-solid fa-user-check me-2"></i> Approvals
                            </a>
                            @endif
                        </li>
                    @endhasanyrole

                    @hasanyrole('admin|system_admin')
                    <hr class="text-secondary">
                    <li class="nav-item mb-2 text-secondary px-3 small uppercase">Administration</li>
                    <li class="nav-item mb-2">
                        <a href="{{ route('permissions.index') }}" class="nav-link text-white">
                            <i class="fa-solid fa-lock me-2"></i> Permissions
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="{{ route('roles.index') }}" class="nav-link text-white">
                            <i class="fa-solid fa-user-shield me-2"></i> Roles
                        </a>
                    </li>
                    @endhasanyrole
                </ul>
            </div>

            <div class="container-fluid m-3">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0">Welcome to <span class="text-primary">{{ Auth::user()->name }}</span>!</h2>

                    <div class="dropdown">
                        <div class="d-flex justify-content-end mb-3">
                            <button id="theme-toggle" class="btn btn-link text-white shadow-none me-2 p-0">
                            <i id="theme-icon" class="fa-solid fa-moon fs-5"></i>
                        </button>
                        </div>
                        <div class="d-flex align-items-center bg-black-subtle p-2 rounded-pill shadow-sm" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;">

                            @if(Auth::user()->profile_image)
                                <img src="{{ asset('storage/' . Auth::user()->profile_image) }}"
                                    alt="Profile" class="rounded-circle border border-primary me-2"
                                    style="width: 40px; height: 40px; object-fit: cover;">
                            @else
                                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=random&color=fff"
                                    alt="Profile" class="rounded-circle border border-primary me-2"
                                    style="width: 40px; height: 40px;">
                            @endif

                            <div class="text-start me-3">
                                <span class="d-block fw-bold text-info" style="font-size: 0.9rem;">{{ Auth::user()->name }}</span>
                                <small class="text-primary d-block" style="font-size: 0.7rem;">
                                    {{ Auth::user()->roles->pluck('name')->first() ?: 'user' }}
                                </small>
                            </div>
                            <i class="fa-solid fa-chevron-down text-muted small"></i>
                        </div>
                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark shadow">
                            <li><a class="dropdown-item" href="#"><i class="fa-solid fa-user me-2"></i> Profile</a></li>
                            <li><hr class="dropdown-divider border-secondary"></li>
                            <li>
                            <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fa-solid fa-right-from-bracket me-2"></i> Logout
                            </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                                </form>
                        </ul>
                    </div>
                </div>
                <div class="row g-3 mt-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white p-3 shadow">
                                <h6><i class="fa-solid fa-users me-2"></i> Total Users</h6>
                                <h3>{{ count($allUsers) }}</h3>
                                <span class="badge bg-white text-primary w-50">In Your Dept</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white p-3 shadow">
                                <h6><i class="fa-solid fa-file-signature me-2 text-warning"></i> Leave Requests</h6>
                                <h3>{{ count($leaveRequests) }}</h3>
                                <span class="badge bg-info text-success w-50">Pending Review</span>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card bg-warning text-dark p-3 shadow">
                                <h6><i class="fa-solid fa-plane me-2"></i> Mission Requests</h6>
                                <h3>{{ count($missionRequests) }}</h3>
                                <span class="badge bg-dark text-white w-50">Active Missions</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white p-3 shadow">
                            <h6><i class="fa-solid fa-heart-pulse me-2"></i> Server Status</h6>
                            <h3>99.9%</h3>
                            <span class="badge bg-secondary text-info w-50">Stable</span>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end gap-2 mt-4">
                     @if(Auth::user()->can('create leaverequests'))
                    <a href="{{ route('leave-requests.create') }}" class="btn btn-outline-primary btn-sm">
                        <i class="fa-solid fa-plus"></i> New Leave
                    </a>
                    @endif

                    @if(Auth::user()->can('create missionrequests'))
                        <a href="{{ route('mission-requests.create') }}" class="btn btn-outline-info btn-sm">
                            <i class="fa-solid fa-plus"></i> New Mission
                        </a>
                    @endif

                    @hasanyrole('admin|admin_it|admin_sale')
                        @if(Auth::user()->can('create user'))
                            <a href="{{ route('admin.users.create') }}" class="btn btn-outline-success btn-sm">
                                <i class="fa-solid fa-user-plus"></i> Create User
                            </a>
                        @endif
                    @endhasanyrole
                </div>

                @hasanyrole('admin|admin_it|admin_sale')
                <div class="card mt-4 bg-black-subtle text-white shadow">
                    <div class="card-header border-secondary d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-success"><i class="fa-solid fa-list me-2"></i> User Management</h5>
                        <small class="text-muted ">
                            <span class="text-primary">Department:</span>
                            @foreach(Auth::user()->departments as $dept)
                                <span class="badge bg-outline-secondary border text-info">{{ $dept->name }}</span>
                            @endforeach
                        </small>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-dark table-hover align-middle">
                                <thead class="table-secondary text-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th class="text-center">Photo</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Departments</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($allUsers as $index => $user)
                                        {{-- លក្ខខណ្ឌការពារចុងក្រោយ៖ បើមិនមែន Admin ធំទេ គឺបង្ហាញតែអ្នក Dept ដូចគ្នា --}}
                                        @if(Auth::user()->hasRole('admin') || Auth::user()->departments->intersect($user->departments)->count() > 0)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td class="text-center">
                                                @if($user->profile_image)
                                                    <img src="{{ asset('storage/' . $user->profile_image) }}"
                                                        alt="User" class="rounded-circle border border-secondary"
                                                        style="width: 35px; height: 35px; object-fit: cover;">
                                                @else
                                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random&color=fff"
                                                        alt="User" class="rounded-circle border border-secondary"
                                                        style="width: 35px; height: 35px;">
                                                @endif
                                            </td>
                                            <td>{{ $user->name }}</td>

                                            <td>{{ $user->email }}</td>

                                            <td>
                                                @foreach($user->roles as $role)
                                                    <span class="badge bg-info text-dark">{{ $role->name }}</span>
                                                @endforeach
                                            </td>

                                            <td>
                                                @forelse($user->departments as $dept)
                                                    <span class="badge border border-success text-success">{{ $dept->name }}</span>
                                                @empty
                                                    <span class="text-muted small italic">No Dept Allocated</span>
                                                @endforelse
                                            </td>

                                            <td class="text-center">
                                                <div class="btn-group">
                                                    @can('edit department')
                                                        <a href="{{ route('admin.users.department.edit', $user->id) }}" class="btn btn-sm btn-outline-info" title="Allocate Dept">
                                                            <i class="fa-solid fa-share-nodes"></i>
                                                        </a>
                                                    @endcan

                                                    @can('edit requests')
                                                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-outline-success">
                                                            <i class="fa-solid fa-pen"></i>
                                                        </a>
                                                    @endcan

                                                   @can('delete requests')
                                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete('{{ $user->id }}', '{{ $user->name }}')">
                                                            <i class="fa-solid fa-trash"></i>
                                                        </button>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                        @endif
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted p-4">
                                                <i class="fa-solid fa-user-slash d-block mb-2 fs-3"></i>
                                                No users found in your department.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endhasanyrole
            </div>
        </div>
    </div>
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-black-subtle text-white border-secondary">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title text-danger"><i class="fa-solid fa-trash"></i> Confirm Delete</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete <strong id="deleteUserName" class="text-info"></strong>?
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Yes, Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('js/dashboard.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
