<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create Role</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
      body {
        background-color: #f8f9fa;
        padding-top: 50px;
      }
      .form-card {
        background: #fff;
        border-radius: 8px;
        padding: 30px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        max-width: 800px;
        margin: 0 auto;
      }
      .btn-dark-custom {
        background-color: #1a1d2b;
        border: none;
        padding: 8px 20px;
      }
      .btn-dark-custom:hover {
        background-color: #2d324a;
      }
      label {
        font-weight: 500;
        margin-bottom: 8px;
        color: #333;
      }
    </style>
  </head>
  <body>

    <div class="container">

        <div class="form-card">
            <div class="header d-flex justify-content-between">
                <h2>permission/create</h2>
                <a href="{{ route('permissions.index') }}" class="btn btn-dark btn-dark-custom">Back</a>
            </div>
            <form action="{{ route('permissions.store') }}" method="POST">
                @csrf

                <div class="mb-4 mt-5">
                    <label for="roleName" class="form-label">Name</label>
                    <input value="{{ old('name') }}" type="text"
                           name="name"
                           class="form-control"
                           id="roleName"
                           placeholder="Input Permission"
                           style="height: 45px;">

                    @error('name')
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="btn btn-dark btn-dark-custom">Submit</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
