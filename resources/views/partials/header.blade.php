<header class="app-header">
    <nav class="navbar navbar-expand-lg navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item d-block d-xl-none">
              <a class="nav-link sidebartoggler nav-icon-hover" id="headerCollapse" href="javascript:void(0)">
                <i class="ti ti-menu-2"></i>
              </a>
            </li>
          </ul>
        <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
            <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">
                <li class="nav-item d-none d-md-block">
    <span class="fw-semibold text-dark">👋 Hai, {{ Auth::user()->nama }}</span>
</li>
<li class="nav-item d-block d-md-none">
    <span class="fw-semibold text-dark">👋 Hai, {{ Auth::user()->nama }}</span>
</li>
                {{-- Notification --}}
                <li class="nav-item dropdown me-3">
                    <a class="nav-link position-relative" href="#" id="notificationDropdown"
                        role="button" data-bs-toggle="dropdown">

                        <i class="fas fa-bell fs-5"></i>

                        @if(isset($reminders) && $reminders->count())
                            <span class="notification-badge">
                                {{ $reminders->count() }}
                            </span>
                        @endif
                    </a>

                    <div class="dropdown-menu dropdown-menu-end shadow"
                        style="width:500px; max-height:500px; overflow:auto; ">

                        <div class="px-3 py-2 border-bottom">
                            <strong>Reminder</strong>
                        </div>

                        @if(isset($reminders) && $reminders->count())

                            @foreach($reminders as $reminder)

                                <a href="{{ route('monitoring.index', ['search' => $reminder['judul']]) }}"
                                    class="dropdown-item reminder-item
                                     @if($reminder['sisaHari'] == 2)
                                        reminder-h2
                                    @elseif($reminder['sisaHari'] == 1)
                                        reminder-h1
                                    @elseif($reminder['sisaHari'] == 0)
                                        reminder-h0
                                    @elseif($reminder['sisaHari'] < 0)
                                        reminder-overdue
                                    @endif">

                                    <div class="fw-semibold">
                                        Proposal "{{ $reminder['judul'] }}"
                                    </div>

                                    <small class="text-muted d-block">
                                        Masih menunggu penyelesaian berkas
                                        <b>{{ $reminder['berkas'] }}</b>.
                                    </small>

                                    <small class="text-muted">
                                        Deadline
                                        {{ \Carbon\Carbon::parse($reminder['deadline'])->format('d M Y') }}
                                    </small>

                                </a>

                            @endforeach

                        @else

                            <div class="text-center text-muted py-4">
                                Tidak ada reminder.
                            </div>

                        @endif

                    </div>
                </li>
                
                <li class="nav-item dropdown">
                    <a class="nav-link nav-icon-hover" href="javascript:void(0)" id="drop2"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="{{ asset('images/profile/user-1.jpg') }}" alt="" width="35" height="35"
                            class="rounded-circle">
                    </a>
                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="drop2">
                        <div class="message-body">
                            <a href="javascript:void(0)" class="d-flex align-items-center gap-2 dropdown-item">
                                <i class="ti ti-user fs-6"></i>
                                <p class="mb-0 fs-3">{{ Auth::user()->nama }}</p>
                            </a>
                            <a href="#"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                style="background-color: #78C841; color: white;"
                                class="btn mx-3 mt-2 d-block">
                                Logout
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>

                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
</header>
