    @extends('layouts.admin.master')

    @section('main-content')
    <div class="content container-fluid">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
        <div class="fw-semibold mb-1">Please fix the following:</div>
        <ul class="mb-0">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="page-title">Change Password</h3>
    </div>

    <form method="post" action="{{ route('reset-password-store') }}">
        @csrf
        <div class="row g-3">

        <div class="col-md-3">
        <label class="form-label">Old Password</label>
        <div class="input-group">
            <input type="password" name="old_password" class="form-control" id="old_password" required>
            <span class="input-group-text" onclick="togglePassword('old_password')">
                <i class="las la-eye" id="old_password_icon"></i>
            </span>
        </div>
    </div>

    <div class="col-md-3">
        <label class="form-label">New Password</label>
        <div class="input-group">
            <input type="password" name="new_password" class="form-control" id="new_password" required>
            <span class="input-group-text" onclick="togglePassword('new_password')">
                <i class="las la-eye" id="new_password_icon"></i>
            </span>
        </div>
    </div>

    <div class="col-md-3">
        <label class="form-label">Confirm Password</label>
        <div class="input-group">
            <input type="text" name="new_password_confirmation" class="form-control" id="confirm_password" required>
            {{-- <span class="input-group-text" onclick="togglePassword('confirm_password')">
                <i class="las la-eye" id="confirm_password_icon"></i>
            </span> --}}
        </div>
    </div>


        </div>

        <div class="mt-3">
        <button class="btn btn-primary">Reset Password</button>
        <a href="{{ route('org.category.index') }}" class="btn btn-link">Cancel</a>
        </div>
    </form>
    </div>


    @endsection
    @section('scripts')
    <script>
    function togglePassword(fieldId) {
    const input = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + '_icon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('la-eye');       
        icon.classList.add('la-eye-slash');   
    } else {
        input.type = 'password';
        icon.classList.remove('la-eye-slash');    
        icon.classList.add('la-eye');       
    }
    }

    </script>
    @endsection
