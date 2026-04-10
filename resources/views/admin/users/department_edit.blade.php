<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Department Allocate</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link href="{{ asset('css/department_edit.css') }}" rel="stylesheet">
</head>
<body>

    <div class="container ">
        <h1>Edit Department for {{ $user->name }}</h1>

        <form action="{{ route('admin.users.department.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row mt-5">
            @foreach($allDepartments as $department)
                <div class="col-md-4 mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox"
                            name="department_ids[]"
                            value="{{ $department->id }}"
                            id="dept_{{ $department->id }}"
                            {{-- ឆែកមើលថា បើ User មាន Department នេះហើយ ឱ្យវា Check ស្រាប់ --}}
                            @if($user->departments->contains($department->id)) checked @endif>

                        <label class="form-check-label" for="dept_{{ $department->id }}">
                            {{ $department->name }}
                        </label>
                    </div>
                </div>
            @endforeach
        </form>
        <div class="mt-5">
            <a href="{{ route('dashboard', $user->id) }}" class="btn btn-sm btn-outline-info" title="Assign Department">
            <i class="fa-solid fa-share-nodes"></i>cencel
            </a>
            <button type="submit" class="btn btn-primary">Update Departments</button>

        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  </body>
</html>
