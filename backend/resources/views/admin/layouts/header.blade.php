<div class="admin-header d-flex align-items-center justify-content-between px-4">
    <div class="d-flex align-items-center">
        <!-- Mobile Sidebar Toggle -->
        <button class="btn btn-link d-md-none sidebar-toggle me-3">
            <i class="fas fa-bars fs-5"></i>
        </button>
        
        <h5 class="mb-0 fw-bold">@yield('page-title', 'Dashboard')</h5>
    </div>
    
    <div class="d-flex align-items-center">
        <!-- Notifications -->
        <div class="dropdown me-3">
            <button class="btn btn-link text-dark position-relative" type="button" data-bs-toggle="dropdown">
                <i class="fas fa-bell fs-5"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem;">
                    {{ \App\Models\User::where('status', 'under_review')->count() }}
                </span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><h6 class="dropdown-header">Notifications</h6></li>
                <li><a class="dropdown-item" href="{{ route('admin.tutors.index', ['status' => 'under_review']) }}">
                    <i class="fas fa-user-clock me-2 text-warning"></i>
                    {{ \App\Models\User::where('status', 'under_review')->count() }} tutors pending review
                </a></li>
            </ul>
        </div>
        
        <!-- Admin Profile -->
        <div class="dropdown">
            <button class="btn btn-link text-dark d-flex align-items-center" type="button" data-bs-toggle="dropdown">
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 36px; height: 36px;">
                    <i class="fas fa-user"></i>
                </div>
                <span class="fw-semibold">{{ Auth::guard('admin')->user()->name }}</span>
                <i class="fas fa-chevron-down ms-2 small"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="#">
                    <i class="fas fa-user me-2"></i>Profile
                </a></li>
                <li><a class="dropdown-item" href="#">
                    <i class="fas fa-cog me-2"></i>Settings
                </a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form action="{{ route('admin.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</div>
