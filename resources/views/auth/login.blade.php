<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Transport Management System</title>
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light" style="
    background-image: url('{{ asset('dist/img/port-silo-1.png') }}');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    min-height: 180vh;
    backdrop-filter: blur(1px);
">

<div class="container">
    <div class="row justify-content-center" style="margin-top:100px;">
        <div class="col-md-4">
            <div class="card shadow-lg border-0" style="background-color: rgba(30, 30, 30, 0.8); color: #f1f1f1;">
                <div class="card-header text-center" style="background-color: rgba(53, 140, 163, 0.9); color: #ffffff;">
                    <h4 style="letter-spacing: 2px;">TMS</h4>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form method="POST" action="{{ url('login') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control bg-dark text-light border-secondary" name="email" id="email" required autofocus
                                   value="{{ old('email') }}">
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control bg-dark text-light border-secondary" name="password" id="password" required>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label text-light" for="remember">Remember Me</label>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary" style="background-color: #2c5f8a; border-color: #2c5f8a;">Login</button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center text-light" style="background-color: rgba(45, 70, 100, 0.9);">
                    &copy; {{ date('Y') }} MTI
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS CDN -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
