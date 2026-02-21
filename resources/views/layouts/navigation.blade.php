@php
    $navNotifications = [];
    if (isset($notifications) && $notifications instanceof \Illuminate\Support\Collection) {
        $navNotifications = $notifications->map(function($notif) {
            return [
                'id' => $notif->id,
                'title' => $notif->title,
                'message' => $notif->message,
                'created_at' => $notif->created_at->diffForHumans(),
                'is_read' => (bool) $notif->is_read,
                'link' => $notif->link ?? null,
            ];
        })->toArray();
    }

    $dashboardRoute = match(Auth::user()->role) {
        'admin'   => route('admin.dashboard'),
        'trainer' => route('trainer.dashboard'),
        'trainee' => route('trainee.dashboard'),
        default   => '/',
    };
@endphp

<style>
    /* ══════════════════════════════════════════
       NAV DROPDOWN TOKENS — Light
       TESDA Color Palette:
       Navy Blue:   #003087
       Royal Blue:  #0057B8
       Accent Red:  #CE1126
    ══════════════════════════════════════════ */
    :root {
        --nd-surface:    #ffffff;
        --nd-surface2:   #f0f5ff;
        --nd-surface3:   #dde8f8;
        --nd-border:     #c5d8f5;
        --nd-text:       #001a4d;
        --nd-text-sec:   #1a3a6b;
        --nd-muted:      #5a7aaa;
        --nd-shadow:     0 4px 16px rgba(0,48,135,0.12), 0 2px 6px rgba(0,48,135,0.07);
        --nd-shadow-lg:  0 20px 48px rgba(0,48,135,0.18), 0 4px 12px rgba(0,48,135,0.10);
        --nd-accent:     #CE1126;
        --nd-accent-bg:  #fff0f2;
        --nd-unread-bg:  #e8f0fb;
        --nd-unread-dot: #0057B8;
        --nd-read-bg:    #f0fdf4;
        --nd-read-dot:   #22c55e;
    }

    /* ══════════════════════════════════════════
       NAV DROPDOWN TOKENS — Dark
    ══════════════════════════════════════════ */
    .dark {
        --nd-surface:    #0a1628;
        --nd-surface2:   #0d1f3c;
        --nd-surface3:   #122550;
        --nd-border:     #1e3a6b;
        --nd-text:       #dde8ff;
        --nd-text-sec:   #adc4f0;
        --nd-muted:      #6b8abf;
        --nd-shadow:     0 4px 16px rgba(0,0,0,0.50);
        --nd-shadow-lg:  0 20px 48px rgba(0,0,0,0.60), 0 4px 12px rgba(0,0,0,0.40);
        --nd-accent:     #e53250;
        --nd-accent-bg:  rgba(206,17,38,0.12);
        --nd-unread-bg:  rgba(0,87,184,0.15);
        --nd-unread-dot: #5b9cf6;
        --nd-read-bg:    rgba(34,197,94,0.08);
        --nd-read-dot:   #4ade80;
    }

    /* ── Shared dropdown shell ── */
    .nd-dropdown {
        position: absolute;
        right: 0;
        top: calc(100% + 12px);
        border-radius: 14px;
        border: 1px solid var(--nd-border);
        background: var(--nd-surface);
        box-shadow: var(--nd-shadow-lg);
        overflow: hidden;
        z-index: 50;
    }

    /* ── Dropdown top accent bar — TESDA tricolor ── */
    .nd-accent-bar {
        height: 4px;
        background: linear-gradient(90deg,
            #CE1126 0%, #CE1126 33%,
            #0057B8 33%, #0057B8 66%,
            #F5C518 66%, #F5C518 100%
        );
    }

    /* ── Section headers inside dropdowns ── */
    .nd-dropdown-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 16px 10px;
        border-bottom: 1px solid var(--nd-border);
        background: var(--nd-surface2);
    }
    .nd-dropdown-header-title {
        font-size: 13px;
        font-weight: 700;
        color: var(--nd-text);
        letter-spacing: 0.2px;
    }
    .nd-dropdown-header-meta {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        color: var(--nd-muted);
    }

    /* ══════════════════════════════════════════
       NOTIFICATION DROPDOWN
    ══════════════════════════════════════════ */
    .nd-notif-dropdown { width: 340px; }

    .nd-notif-list { max-height: 380px; overflow-y: auto; }
    .nd-notif-list::-webkit-scrollbar { width: 4px; }
    .nd-notif-list::-webkit-scrollbar-thumb { background: var(--nd-border); border-radius: 4px; }

    .nd-notif-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 13px 16px;
        border-bottom: 1px solid var(--nd-border);
        text-decoration: none;
        transition: background .14s;
        cursor: pointer;
    }
    .nd-notif-item:last-child { border-bottom: none; }
    .nd-notif-item:hover { background: var(--nd-surface2); }
    .nd-notif-item.unread { background: var(--nd-unread-bg); }
    .nd-notif-item.unread:hover { filter: brightness(0.97); }

    .nd-notif-icon {
        width: 34px; height: 34px; border-radius: 9px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0; font-size: 14px;
        transition: background .14s;
    }
    .nd-notif-icon.unread { background: rgba(0,87,184,.15); color: var(--nd-unread-dot); }
    .nd-notif-icon.read   { background: var(--nd-surface3); color: var(--nd-muted); }

    .nd-notif-body { flex: 1; min-width: 0; }
    .nd-notif-title {
        font-size: 13px; font-weight: 700;
        color: var(--nd-text); line-height: 1.35;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .nd-notif-msg {
        font-size: 12px; color: var(--nd-text-sec);
        margin-top: 2px; line-height: 1.4;
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
    }
    .nd-notif-time {
        font-size: 11px; color: var(--nd-muted); margin-top: 4px;
    }

    .nd-notif-dot {
        width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; margin-top: 5px;
    }
    .nd-notif-dot.unread { background: var(--nd-unread-dot); }
    .nd-notif-dot.read   { background: transparent; border: 1.5px solid var(--nd-border); }

    /* Empty state */
    .nd-notif-empty {
        padding: 40px 16px; text-align: center; color: var(--nd-muted);
    }
    .nd-notif-empty i { font-size: 28px; opacity: .35; display: block; margin-bottom: 10px; }
    .nd-notif-empty p { font-size: 13px; }

    /* ══════════════════════════════════════════
       PROFILE DROPDOWN
    ══════════════════════════════════════════ */
    .nd-profile-dropdown { width: 300px; }

    /* Profile hero section */
    .nd-profile-hero {
        padding: 16px;
        background: var(--nd-surface2);
        border-bottom: 1px solid var(--nd-border);
        display: flex;
        align-items: center;
        gap: 14px;
    }
    .nd-profile-avatar {
        width: 46px; height: 46px; border-radius: 12px;
        background: linear-gradient(135deg, #0057B8 0%, #003087 100%);
        display: flex; align-items: center; justify-content: center;
        font-size: 18px; font-weight: 800; color: #fff;
        flex-shrink: 0; position: relative;
    }
    .nd-profile-avatar-dot {
        position: absolute; bottom: -2px; right: -2px;
        width: 12px; height: 12px;
        background: #22c55e;
        border: 2px solid var(--nd-surface2);
        border-radius: 50%;
    }
    .nd-profile-name {
        font-size: 14px; font-weight: 700; color: var(--nd-text); line-height: 1.3;
    }
    .nd-profile-role {
        display: inline-flex; align-items: center; gap: 4px;
        margin-top: 4px;
        padding: 2px 8px; border-radius: 20px;
        font-size: 11px; font-weight: 600;
        background: var(--nd-accent-bg);
        color: var(--nd-accent);
        border: 1px solid rgba(206,17,38,.2);
    }
    .dark .nd-profile-role { border-color: rgba(229,50,80,.25); }

    /* Menu items */
    .nd-menu-body { padding: 8px; }

    .nd-menu-item {
        display: flex; align-items: center; gap: 12px;
        padding: 10px 12px; border-radius: 9px;
        font-size: 13px; font-weight: 600;
        color: var(--nd-text-sec);
        text-decoration: none;
        cursor: pointer; border: none; width: 100%;
        background: none; text-align: left;
        transition: background .14s, color .14s;
    }
    .nd-menu-item:hover { background: var(--nd-surface2); color: var(--nd-text); }

    .nd-menu-icon {
        width: 32px; height: 32px; border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: 13px; flex-shrink: 0;
        transition: transform .15s;
    }
    .nd-menu-item:hover .nd-menu-icon { transform: scale(1.1); }

    .nd-menu-icon.blue   { background: #e8f0fb; color: #0057B8; }
    .nd-menu-icon.green  { background: #f0fdf4; color: #16a34a; }
    .nd-menu-icon.red    { background: #fff0f2; color: #CE1126; }
    .dark .nd-menu-icon.blue  { background: rgba(91,156,246,.15);  color: #5b9cf6; }
    .dark .nd-menu-icon.green { background: rgba(74,222,128,.12);  color: #4ade80; }
    .dark .nd-menu-icon.red   { background: rgba(229,50,80,.12);   color: #e53250; }

    .nd-menu-item-label { flex: 1; }
    .nd-menu-item-label span { display: block; }
    .nd-menu-item-label .sub { font-size: 11px; font-weight: 400; color: var(--nd-muted); margin-top: 1px; }

    /* Dark mode toggle switch */
    .nd-toggle-track {
        width: 36px; height: 20px; border-radius: 10px;
        background: var(--nd-surface3); border: 1px solid var(--nd-border);
        position: relative; flex-shrink: 0;
        transition: background .2s;
    }
    .nd-toggle-track.on { background: #22c55e; border-color: #22c55e; }
    .nd-toggle-thumb {
        position: absolute; top: 2px; left: 2px;
        width: 14px; height: 14px; border-radius: 50%;
        background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,.2);
        transition: transform .2s;
    }
    .nd-toggle-track.on .nd-toggle-thumb { transform: translateX(16px); }

    /* Divider */
    .nd-divider { height: 1px; background: var(--nd-border); margin: 4px 8px; }

    /* Footer */
    .nd-dropdown-footer {
        padding: 8px 16px;
        border-top: 1px solid var(--nd-border);
        background: var(--nd-surface2);
        text-align: center;
        font-size: 11px;
        color: var(--nd-muted);
    }
</style>

<div x-data="navigationComponent()" x-init="init()">
    <nav class="fixed top-0 left-0 right-0 z-50 border-b shadow-lg"
         style="background: linear-gradient(90deg, #003087 0%, #0057B8 50%, #003087 100%); border-bottom: 3px solid #CE1126;"
         @scroll.window="isScrolled = (window.pageYOffset > 10)"
         :class="{ 'backdrop-blur-md': isScrolled }">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">

                <!-- Left Side -->
                <div class="flex items-center gap-4">
                    <button @click="$store.sidebar.toggle()"
                        class="p-3 rounded-xl text-white shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200 group"
                        style="background: linear-gradient(135deg, #CE1126 0%, #A50E1E 100%);">
                        <i class="fas fa-bars-staggered transition-transform duration-200 group-hover:rotate-12"></i>
                    </button>

                    <a href="{{ $dashboardRoute }}" class="flex items-center gap-4">
                        <div class="h-12 w-12 bg-white rounded-2xl flex items-center justify-center shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300">
                            <img src="{{ asset('assets/app_logo.PNG') }}" alt="App Logo" class="h-8 w-8 object-contain" />
                        </div>
                        <div>
                            <span class="text-2xl font-black tracking-tight text-white block">
                                {{ config('app.name', 'Laravel') }}
                            </span>
                            <span class="text-xs font-medium tracking-widest uppercase block"
                                  style="color: #a8c4f0; margin-top: -2px;">
                                Skills Development Authority
                            </span>
                        </div>
                    </a>
                </div>

                <!-- Right Side -->
                <div class="flex items-center gap-3">

                    <!-- ══════════════════════════════════
                         NOTIFICATION BELL + DROPDOWN
                    ══════════════════════════════════ -->
                    <div class="relative">
                        <button @click="toggleNotifications()"
                                class="relative p-2 rounded-xl transition-colors duration-200"
                                style="background: rgba(255,255,255,0.10); border: 1px solid rgba(255,255,255,0.15);"
                                onmouseover="this.style.background='rgba(255,255,255,0.20)'"
                                onmouseout="this.style.background='rgba(255,255,255,0.10)'">
                            <i class="fas fa-bell text-white text-xl"></i>
                            <div x-show="unreadCount > 0"
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0 scale-0"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 class="absolute -top-1 -right-1 w-5 h-5 rounded-full flex items-center justify-center animate-pulse"
                                 style="background: #CE1126; border: 2px solid #003087;">
                                <span class="text-white text-xs font-bold" x-text="unreadCount"></span>
                            </div>
                        </button>

                        <!-- Notification Dropdown -->
                        <div x-show="notificationOpen"
                             @click.away="notificationOpen = false"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                             class="nd-dropdown nd-notif-dropdown">

                            <!-- Accent bar -->
                            <div class="nd-accent-bar"></div>

                            <!-- Header -->
                            <div class="nd-dropdown-header">
                                <span class="nd-dropdown-header-title">
                                    <i class="fas fa-bell mr-2" style="color: #0057B8;"></i>Notifications
                                </span>
                                <div class="nd-dropdown-header-meta">
                                    <div x-show="loading">
                                        <div class="animate-spin rounded-full h-3.5 w-3.5 border-b-2" style="border-color: #0057B8;"></div>
                                    </div>
                                    <span x-show="unreadCount > 0"
                                          class="px-2 py-0.5 rounded-full text-xs font-700"
                                          style="background: var(--nd-unread-bg); color: var(--nd-unread-dot);"
                                          x-text="unreadCount + ' unread'">
                                    </span>
                                    <span x-show="!loading && allRead"
                                          style="color: var(--nd-read-dot);">
                                        <i class="fas fa-check-circle mr-1"></i>All read
                                    </span>
                                </div>
                            </div>

                            <!-- List -->
                            <div class="nd-notif-list">
                                <template x-for="notif in notifications" :key="notif.id">
                                    <a :href="notif.link || '#'"
                                       @click="notif.link ? handleNotificationClick($event, notif) : $event.preventDefault()"
                                       class="nd-notif-item"
                                       :class="notif.is_read ? 'read' : 'unread'">

                                        <!-- Icon -->
                                        <div class="nd-notif-icon" :class="notif.is_read ? 'read' : 'unread'">
                                            <i class="fas fa-bell"></i>
                                        </div>

                                        <!-- Body -->
                                        <div class="nd-notif-body">
                                            <div class="nd-notif-title" x-text="notif.title"></div>
                                            <div class="nd-notif-msg" x-text="notif.message"></div>
                                            <div class="nd-notif-time" x-text="notif.created_at"></div>
                                        </div>

                                        <!-- Dot indicator -->
                                        <div class="nd-notif-dot" :class="notif.is_read ? 'read' : 'unread'"></div>
                                    </a>
                                </template>

                                <!-- Empty state -->
                                <div x-show="notifications.length === 0" class="nd-notif-empty">
                                    <i class="fas fa-bell-slash"></i>
                                    <p>No notifications yet</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ══════════════════════════════════
                         PROFILE BUTTON + DROPDOWN
                    ══════════════════════════════════ -->
                    <div class="relative">
                        <button @click="profileOpen = !profileOpen"
                                class="flex items-center gap-3 px-3 py-2 rounded-xl transition-all duration-200 group"
                                style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.18);"
                                onmouseover="this.style.background='rgba(255,255,255,0.18)'"
                                onmouseout="this.style.background='rgba(255,255,255,0.08)'">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center shadow"
                                 style="background: linear-gradient(135deg, #CE1126 0%, #A50E1E 100%);">
                                <span class="text-sm font-bold text-white" x-text="userInitial">
                                    {{ Auth::check() ? substr(Auth::user()->name, 0, 1) : 'G' }}
                                </span>
                            </div>
                            <div class="hidden sm:block text-left">
                                <div class="text-sm font-bold text-white leading-tight" x-text="userName">
                                    {{ Auth::check() ? Auth::user()->name : 'Guest' }}
                                </div>
                                <div class="text-xs leading-tight" style="color: #a8c4f0;">System User</div>
                            </div>
                            <i class="fas fa-chevron-down text-xs transition-transform duration-200"
                               style="color: rgba(255,255,255,0.65);"
                               :class="{ 'rotate-180': profileOpen }"></i>
                        </button>

                        <!-- Profile Dropdown -->
                        <div x-show="profileOpen"
                             @click.away="profileOpen = false"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                             class="nd-dropdown nd-profile-dropdown">

                            <!-- Accent bar -->
                            <div class="nd-accent-bar"></div>

                            <!-- Profile hero -->
                            <div class="nd-profile-hero">
                                <div class="nd-profile-avatar">
                                    <span x-text="userInitial">{{ Auth::check() ? substr(Auth::user()->name, 0, 1) : 'G' }}</span>
                                    <div class="nd-profile-avatar-dot"></div>
                                </div>
                                <div>
                                    <div class="nd-profile-name" x-text="userName">{{ Auth::check() ? Auth::user()->name : 'Guest' }}</div>
                                    <div class="nd-profile-role">
                                        <i class="fas fa-shield-halved" style="font-size: 9px;"></i> System User
                                    </div>
                                </div>
                            </div>

                            <!-- Menu -->
                            <div class="nd-menu-body">

                                <!-- My Profile -->
                                <a :href="profileEditRoute" class="nd-menu-item">
                                    <div class="nd-menu-icon blue">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="nd-menu-item-label">
                                        <span>My Profile</span>
                                        <span class="sub">Manage account settings</span>
                                    </div>
                                    <i class="fas fa-chevron-right" style="font-size: 11px; color: var(--nd-muted);"></i>
                                </a>

                                <!-- Dark Mode Toggle -->
                                <button @click="$store.darkMode.toggle()" class="nd-menu-item">
                                    <div class="nd-menu-icon green">
                                        <template x-if="$store.darkMode.on">
                                            <i class="fas fa-sun" style="color: #f59e0b;"></i>
                                        </template>
                                        <template x-if="!$store.darkMode.on">
                                            <i class="fas fa-moon"></i>
                                        </template>
                                    </div>
                                    <div class="nd-menu-item-label">
                                        <span x-text="$store.darkMode.on ? 'Light Mode' : 'Dark Mode'">Dark Mode</span>
                                        <span class="sub">Switch theme appearance</span>
                                    </div>
                                    <!-- Toggle switch -->
                                    <div class="nd-toggle-track" :class="{ 'on': $store.darkMode.on }">
                                        <div class="nd-toggle-thumb"></div>
                                    </div>
                                </button>

                                <div class="nd-divider"></div>

                                <!-- Logout -->
                                <button @click="logout()" class="nd-menu-item">
                                    <div class="nd-menu-icon red">
                                        <i class="fas fa-sign-out-alt"></i>
                                    </div>
                                    <div class="nd-menu-item-label">
                                        <span>Sign Out</span>
                                        <span class="sub">End your current session</span>
                                    </div>
                                    <i class="fas fa-chevron-right" style="font-size: 11px; color: var(--nd-muted);"></i>
                                </button>
                            </div>

                            <!-- Footer -->
                            <div class="nd-dropdown-footer">
                                {{ config('app.name') }} &nbsp;·&nbsp; v1.0
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </nav>
</div>

<script>
function navigationComponent() {
    return {
        isScrolled: false,
        notificationOpen: false,
        profileOpen: false,
        loading: false,
        allRead: false,

        userName: @json(Auth::check() ? Auth::user()->name : 'Guest'),
        userInitial: @json(Auth::check() ? substr(Auth::user()->name, 0, 1) : 'G'),
        unreadCount: 0,
        notifications: @json($navNotifications),

        profileEditRoute: '{{ route("profile.edit") }}',
        logoutRoute: '{{ route("logout") }}',
        csrfToken: '{{ csrf_token() }}',

        init() {
            this.updateUnreadCount();
            this.updateAllReadStatus();
        },

        toggleNotifications() {
            this.notificationOpen = !this.notificationOpen;
        },

        handleNotificationClick(event, notif) {
            if (!notif.is_read) {
                notif.is_read = true;
                this.updateUnreadCount();
                this.updateAllReadStatus();
            }
            this.notificationOpen = false;
        },

        updateUnreadCount() {
            this.unreadCount = this.notifications.filter(n => !n.is_read).length;
        },

        updateAllReadStatus() {
            this.allRead = this.notifications.length > 0 && this.unreadCount === 0;
        },

        logout() {
            if (confirm('Are you sure you want to log out?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = this.logoutRoute;
                const csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = this.csrfToken;
                form.appendChild(csrf);
                document.body.appendChild(form);
                form.submit();
            }
        }
    }
}
</script>
