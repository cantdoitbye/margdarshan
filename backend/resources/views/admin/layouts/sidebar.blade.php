<div class="admin-sidebar">
    <!-- Logo -->
    <div class="logo">
        <i class="fas fa-graduation-cap me-2"></i>
        TutorSphere Admin
    </div>
    
    <!-- Navigation -->
    <nav class="mt-3">
        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>
        
        <a href="{{ route('admin.questions.index') }}" class="nav-link {{ request()->routeIs('admin.questions.*') ? 'active' : '' }}">
            <i class="fas fa-question-circle"></i>
            <span>Questions</span>
        </a>
        
        <a href="{{ route('admin.tutors.index') }}" class="nav-link {{ request()->routeIs('admin.tutors.*') ? 'active' : '' }}">
            <i class="fas fa-users"></i>
            <span>Tutors</span>
        </a>
        
        <!-- Master Data Section -->
        <div class="nav-section-title mt-3 mb-2 px-3 text-uppercase" style="font-size: 0.75rem; color: rgba(255,255,255,0.5); font-weight: 600;">
            Master Data
        </div>
        
        <a href="{{ route('admin.boards.index') }}" class="nav-link {{ request()->routeIs('admin.boards.*') ? 'active' : '' }}">
            <i class="fas fa-clipboard-list"></i>
            <span>Boards</span>
        </a>
        
        <a href="{{ route('admin.classes.index') }}" class="nav-link {{ request()->routeIs('admin.classes.*') ? 'active' : '' }}">
            <i class="fas fa-layer-group"></i>
            <span>Classes</span>
        </a>
        
        <a href="{{ route('admin.subjects.index') }}" class="nav-link {{ request()->routeIs('admin.subjects.*') ? 'active' : '' }}">
            <i class="fas fa-book"></i>
            <span>Subjects</span>
        </a>
        
        <!-- Quiz Management Section -->
        <div class="nav-section-title mt-3 mb-2 px-3 text-uppercase" style="font-size: 0.75rem; color: rgba(255,255,255,0.5); font-weight: 600;">
            Quiz Management
        </div>
        
        <a href="{{ route('admin.chapters.index') }}" class="nav-link {{ request()->routeIs('admin.chapters.*') ? 'active' : '' }}">
            <i class="fas fa-book-open"></i>
            <span>Chapters</span>
        </a>
        
        <a href="{{ route('admin.quizzes.index') }}" class="nav-link {{ request()->routeIs('admin.quizzes.*') ? 'active' : '' }}">
            <i class="fas fa-clipboard-question"></i>
            <span>Quizzes</span>
        </a>
        
        <a href="#" class="nav-link">
            <i class="fas fa-cog"></i>
            <span>Settings</span>
        </a>
        
        <hr class="my-3" style="border-color: rgba(255,255,255,0.1);">
        
        <form action="{{ route('admin.logout') }}" method="POST">
            @csrf
            <button type="submit" class="nav-link w-100 border-0 bg-transparent text-start">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </button>
        </form>
    </nav>
</div>
