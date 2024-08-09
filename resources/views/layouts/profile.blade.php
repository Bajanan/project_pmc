<div class="d-flex align-items-center">
    <a class="ms-3" id="dropdownMenuLink"  href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <div class="profile-btn d-flex align-items-center">
            <img src="{{ asset('images/user.png') }}" class="profile-pic" />
        </div>
    </a>
    <div class="dropdown-menu">
        <p class="dropdown-item">Hi, {{ Auth::user()->name }}</p>
        <a class="dropdown-item text-danger" href="{{ route('logout') }}"
            onclick="event.preventDefault();
                            document.getElementById('logout-form').submit();"><i class="fa fa-right-from-bracket me-2"></i>
            {{ __('Logout') }}
        </a>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>
    </div>
</div>


