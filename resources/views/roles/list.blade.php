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

            <div class="d-flex justify-content-between mb-3">
                <h2>Role List</h2>
                @can('insert')
                    <a href="{{ route('roles.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Create Role</a>
                @endcan
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
                            <td>{{ $loop->iteration }}</td>
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
