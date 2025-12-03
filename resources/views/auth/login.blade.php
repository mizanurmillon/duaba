<!DOCTYPE html>
<html lang="en">

<head>
    <title>Login</title>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    @include('backend.partials.style')
</head>

<body style="background: #004C62;">
    <div class="my-5 account-pages pt-sm-5">
        <div class="container">
            <div class="d-flex justify-content-center align-items-center" style="width: 100%; height: 80vh">
                <div style="width: 500px">
                    <div class="overflow-hidden card">
                        <div class="pt-0 card-body">
                            <div class="px-2 py-4">
                                <form class="form-horizontal" action="{{ route('login') }}" method="POST">
                                    @csrf
                                    <div class="mb-10 text-center">
                                        <h1 class="mb-3 text-dark"> Sign In </h1>
                                    </div>

                                    <div class="mb-10 fv-row">
                                        @if (session('status'))
                                            {{ session('status') }}
                                        @endif
                                    </div>

                                    <div class="mb-3">
                                        <label for="email" class="form-label fs-5">Email</label>
                                        <input type="text" name="email" class="form-control" id="email"
                                            placeholder="Enter Email">
                                        @error('email')
                                            <span class="d-block text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fs-5">Password</label>
                                        <div class="input-group auth-pass-inputgroup">
                                            <input type="password" class="form-control" name="password"
                                                placeholder="Enter password" aria-label="Password"
                                                aria-describedby="password-addon">
                                            <button class="btn btn-light " type="button" id="password-addon">
                                                <i class="mdi mdi-eye-outline"></i>
                                            </button>
                                        </div>
                                        @error('password')
                                            <span class="d-block text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="mt-4 d-grid">
                                        <button class="btn btn-primary waves-effect waves-light" type="submit">Log
                                            In</button>
                                        <div class="mt-4 text-center">
                                            <a href="{{ route('password.request') }}" class="text-muted"><i
                                                    class="mdi mdi-lock me-1"></i> Forgot your password?</a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('backend.partials.script')
</body>

</html>
