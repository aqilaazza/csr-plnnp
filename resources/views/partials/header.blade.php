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
                <li class="nav-item dropdown me">
                    <a class="nav-link position-relative"
                        href="#"
                        id="notificationDropdown"
                        role="button"
                        data-bs-toggle="dropdown">

                        <i class="fas fa-bell fs-5"></i>

                        @if($reminders->count())
                            <span class="notification-badge">
                                {{ $reminders->count() }}
                            </span>
                        @endif

                    </a>

                    <div class="dropdown-menu dropdown-menu-end shadow notification-menu">

                        {{-- Header --}}
                        <div class="px-3 py-3 border-bottom">
                            <h6 class="fw-bold mb-0">
                                🔔 Reminder Proposal
                            </h6>

                            <small class="text-muted">
                                {{ $reminders->count() }} reminder aktif
                            </small>
                        </div>

                        {{-- Filter --}}
                        <div class="px-3 py-2 border-bottom">

                            <div class="d-flex flex-wrap gap-2">

                                <button
                                    class="btn btn-sm btn-success reminder-filter active"
                                    data-filter="all">
                                    Semua ({{ $reminders->count() }})
                                </button>

                                <button
                                    class="btn btn-sm btn-orange reminder-filter"
                                    data-filter="today">
                                    Hari Ini ({{ $reminderGroups['today']->count() }})
                                </button>

                                <button
                                    class="btn btn-sm btn-danger reminder-filter"
                                    data-filter="h1">
                                    H-1 ({{ $reminderGroups['h1']->count() }})
                                </button>

                                <button
                                    class="btn btn-sm btn-warning text-dark reminder-filter"
                                    data-filter="h2">
                                    H-2 ({{ $reminderGroups['h2']->count() }})
                                </button>

                                <button
                                    class="btn btn-sm btn-secondary reminder-filter"
                                    data-filter="overdue">
                                    Terlambat ({{ $reminderGroups['overdue']->count() }})
                                </button>

                            </div>

                        </div>

                        {{-- Reminder Aktif --}}
                        @forelse($reminders as $reminder)

                            @php

                                $filterClass='';

                                if($reminder['sisaHari']==0){
                                    $filterClass='today';
                                }elseif($reminder['sisaHari']==1){
                                    $filterClass='h1';
                                }elseif($reminder['sisaHari']==2){
                                    $filterClass='h2';
                                }elseif($reminder['sisaHari']<0){
                                    $filterClass='overdue';
                                }

                            @endphp

                            <a href="{{ route('monitoring.index',['search'=>$reminder['judul']]) }}"
                                class="dropdown-item reminder-item {{ $filterClass }}

                                @if($reminder['sisaHari']==0)
                                    reminder-h0
                                @elseif($reminder['sisaHari']==1)
                                    reminder-h1
                                @elseif($reminder['sisaHari']==2)
                                    reminder-h2
                                @elseif($reminder['sisaHari']<0)
                                    reminder-overdue
                                @endif">

                                <div class="fw-semibold">
                                    Proposal "{{ $reminder['judul'] }}"
                                </div>

                                <small class="text-muted d-block">
                                    Menunggu penyelesaian berkas
                                    <b>{{ $reminder['berkas'] }}</b>
                                </small>

                                <small class="text-muted">
                                    📅 Deadline
                                    {{ \Carbon\Carbon::parse($reminder['deadline'])->format('d M Y') }}
                                </small>

                            </a>

                        @empty

                            <div class="text-center py-4 text-muted">
                                Tidak ada reminder aktif.
                            </div>

                        @endforelse

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
