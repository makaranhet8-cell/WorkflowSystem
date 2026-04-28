<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Role List</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
      body {
        background-color: #f8f9fa;
        padding: 40px 0;
      }
      .table-container {
        background: #fff;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
      }
      .table thead th {
        border-top: none;
        background-color: #fff;
        color: #333;
        font-weight: 600;
        padding: 15px;
      }
      .table tbody td {
        padding: 15px;
        vertical-align: middle;
        color: #666;
      }
      /* Custom Button Colors */
      .btn-edit {
        background-color: #1a1d2b;
        color: white;
        border: none;
        padding: 5px 15px;
      }
      .btn-edit:hover {
        background-color: #2d324a;
        color: white;
      }
      .btn-delete {
        background-color: #dc3545;
        color: white;
        border: none;
        padding: 5px 15px;
      }
    </style>
  </head>
  <body>
    <div class="container">
        @if (Session::has('success'))
            <div class="text-success">
                {{ Session::get('success') }}
            </div>
        @endif
        @if (Session::has('error'))
            <div class="text-danger">
                {{ Session::get('error') }}
            </div>
        @endif
        <div class="header d-flex justify-content-end">
           <a href="{{ route('dashboard') }}" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Back to Dashboard</a>
        </div>
        <div class="table-container mt-5">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <form action="{{ route('roles.index') }}" method="GET" class="d-flex w-50">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control me-2" placeholder="Search by name..." value="{{ request('search') }}">
                        <button class="btn btn-outline-primary" type="submit">
                            <i class="fa fa-search"></i> Search
                        </button>
                        @if(request('search'))
                            <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary mx-2">Clear</a>
                        @endif
                    </div>
                </form>
                <a href="{{ route('roles.create') }}" class="btn btn-dark">+ Create</a>
            </div>
            <table class="table table-hover mt-4">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th class="px-5 py-3 text-left">Name</th>
                        <th >Permission</th>
                        <th >Created</th>
                        <th class="px-5 py-3 text-left">Action</th>
                    </tr>
                </thead>
                <tbody>

                    @if($roles->isNotEmpty())
                        @foreach($roles as $role)
                        <tr>
                            <td>{{ $role->id }}</td>
                            <td class="px-5 py-3 text-left">{{ $role->name }}</td>
                            <td>{{ $role->permissions->pluck('name')->implode(',') }}</td>
                            <td>{{ \Carbon\Carbon::parse($role->created_at)->format('d M, Y') }}</td>
                            <td >
                                <div class="d-flex">
                                    <a href="{{ route('roles.edit',$role->id) }}" class="btn btn-sm btn-edit me-2">Edit</a>
                                <form action="{{ route('roles.destroy', $role->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-delete"
                                        onclick="return confirm('តើអ្នកប្រាកដថាចង់លុបវាពិតមែនទេ?')">
                                    Delete
                                </button>
                                </div>
                            </form>
                            </td>
                        </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            <div class="d-flex justify-content-between ">
                <div class="text-muted small">
                    Showing {{ $roles->firstItem() }} to {{ $roles->lastItem() }} of {{ $roles->total() }} results
                </div>
                <div>
                    {{ $roles->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
