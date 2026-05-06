@php
    $usuariEspaiId = session('usuari_espai_id');
    $espaiId = session('espai_id');
    $dinsEspai = request()->routeIs('espai.*');
@endphp

@if($usuariEspaiId && $espaiId && $dinsEspai)

<style>
    .notif-bell-wrap {
        position: fixed; top: 14px; right: 16px; z-index: 1000;
        font-family: 'Figtree', system-ui, -apple-system, sans-serif;
    }
    [x-cloak] { display: none !important; }

    .notif-bell-btn {
        position: relative; width: 44px; height: 44px; border-radius: 50%;
        border: 1px solid rgba(0,0,0,0.08); background: rgba(255,255,255,0.85);
        backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);
        color: #1c1c1e; font-size: 1.2rem; cursor: pointer;
        display: inline-flex; align-items: center; justify-content: center;
        box-shadow: 0 4px 14px rgba(0,0,0,0.08);
        transition: transform .15s ease, box-shadow .15s ease;
    }
    .notif-bell-btn:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(0,0,0,0.12); }
    .notif-bell-btn[data-active="true"] { background: #2563eb; color: #fff; border-color: #1d4ed8; }

    .notif-bell-badge {
        position: absolute; top: -4px; right: -4px;
        min-width: 20px; height: 20px; padding: 0 6px; border-radius: 10px;
        background: #ef4444; color: #fff; font-size: 11px; font-weight: 700;
        display: inline-flex; align-items: center; justify-content: center;
        box-shadow: 0 2px 6px rgba(239,68,68,0.5);
    }

    .notif-bell-panel {
        position: absolute; top: 56px; right: 0; width: 360px;
        max-width: calc(100vw - 32px); max-height: 70vh; overflow: hidden;
        background: rgba(255,255,255,0.96);
        backdrop-filter: blur(18px) saturate(160%);
        -webkit-backdrop-filter: blur(18px) saturate(160%);
        border: 1px solid rgba(0,0,0,0.08); border-radius: 16px;
        box-shadow: 0 16px 40px rgba(0,0,0,0.18);
        display: flex; flex-direction: column;
    }

    .notif-bell-header {
        display: flex; align-items: center; justify-content: space-between;
        padding: 12px 16px; border-bottom: 1px solid rgba(0,0,0,0.06);
    }
    .notif-bell-title { font-weight: 600; font-size: 15px; color: #1c1c1e; }
    .notif-bell-readall {
        background: transparent; border: 0; color: #2563eb;
        font-size: 12px; font-weight: 600; cursor: pointer;
        padding: 4px 6px; border-radius: 6px;
    }
    .notif-bell-readall:hover { background: rgba(37,99,235,0.08); }
    .notif-bell-readall[disabled] { color: #9ca3af; cursor: default; }
    .notif-bell-readall[disabled]:hover { background: transparent; }

    .notif-bell-list { overflow-y: auto; flex: 1; padding: 4px 0; }

    .notif-item {
        display: flex; gap: 10px; padding: 10px 16px;
        text-decoration: none; color: inherit;
        border-bottom: 1px solid rgba(0,0,0,0.04); cursor: pointer;
        transition: background .12s ease;
    }
    .notif-item:hover { background: rgba(0,0,0,0.03); }
    .notif-item--unread { background: rgba(37,99,235,0.05); }
    .notif-item--unread:hover { background: rgba(37,99,235,0.10); }

    .notif-item__icon {
        flex: 0 0 32px; width: 32px; height: 32px; border-radius: 50%;
        background: #eff6ff; color: #2563eb;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 16px;
    }
    .notif-item--noticia .notif-item__icon { background: #fee2e2; color: #dc2626; }
    .notif-item--usuari  .notif-item__icon { background: #f3e8ff; color: #7c3aed; }
    .notif-item--guardia-sol .notif-item__icon { background: #fef3c7; color: #d97706; }
    .notif-item--guardia-acc .notif-item__icon { background: #dcfce7; color: #16a34a; }

    .notif-item__body { flex: 1; min-width: 0; }
    .notif-item__title { font-size: 13px; font-weight: 600; color: #111827; line-height: 1.3; word-wrap: break-word; }
    .notif-item__msg { font-size: 12px; color: #4b5563; margin-top: 2px; line-height: 1.35; word-wrap: break-word; }
    .notif-item__time { font-size: 11px; color: #9ca3af; margin-top: 4px; }

    .notif-bell-empty { padding: 24px 16px; text-align: center; color: #6b7280; font-size: 13px; }

    .notif-bell-footer { border-top: 1px solid rgba(0,0,0,0.06); padding: 8px 16px; text-align: center; }
    .notif-bell-footer a { color: #2563eb; font-size: 12px; font-weight: 600; text-decoration: none; }
    .notif-bell-footer a:hover { text-decoration: underline; }

    @media (prefers-color-scheme: dark) {
        .notif-bell-btn { background: rgba(40,40,42,0.85); color: #f3f4f6; border-color: rgba(255,255,255,0.10); }
        .notif-bell-panel { background: rgba(28,28,30,0.96); border-color: rgba(255,255,255,0.10); }
        .notif-bell-title { color: #f3f4f6; }
        .notif-item__title { color: #f3f4f6; }
        .notif-item__msg { color: #d1d5db; }
        .notif-item:hover { background: rgba(255,255,255,0.05); }
        .notif-item--unread { background: rgba(37,99,235,0.12); }
        .notif-item--unread:hover { background: rgba(37,99,235,0.18); }
    }

    @media (max-width: 480px) {
        .notif-bell-panel { right: -8px; width: calc(100vw - 24px); }
    }
</style>

<div
    x-data="notificationBell({
        pollUrl: @js(route('espai.notificacions.poll')),
        readUrlBase: @js(url('/espai/notificacions')),
        readAllUrl: @js(route('espai.notificacions.readAll')),
        indexUrl: @js(route('espai.notificacions.index')),
        csrf: @js(csrf_token()),
        intervalMs: 20000
    })"
    x-init="init()"
    class="notif-bell-wrap"
>
    <button type="button" class="notif-bell-btn" :data-active="open"
            @click="toggle()" :aria-expanded="open ? 'true' : 'false'"
            aria-label="Notificacions">
        <i class="bi bi-bell-fill" aria-hidden="true"></i>
        <template x-if="unreadCount > 0">
            <span class="notif-bell-badge" x-text="unreadCount > 99 ? '99+' : unreadCount"></span>
        </template>
    </button>

    <div x-show="open" x-transition.opacity
         @click.outside="open = false"
         @keydown.escape.window="open = false"
         class="notif-bell-panel" x-cloak>
        <div class="notif-bell-header">
            <span class="notif-bell-title">Notificacions</span>
            <button type="button" class="notif-bell-readall"
                    @click="markAllRead()" :disabled="unreadCount === 0">
                Marcar totes com a llegides
            </button>
        </div>

        <div class="notif-bell-list">
            <template x-if="items.length === 0">
                <div class="notif-bell-empty">Encara no tens notificacions.</div>
            </template>

            <template x-for="n in items" :key="n.id">
                <a :href="n.url || '#'" @click="onItemClick($event, n)" class="notif-item"
                   :class="{
                        'notif-item--unread': !n.llegida,
                        'notif-item--noticia': n.tipus === 'noticia_creada',
                        'notif-item--usuari': n.tipus === 'usuari_nou',
                        'notif-item--guardia-sol': n.tipus === 'guardia_solicitada',
                        'notif-item--guardia-acc': n.tipus === 'guardia_acceptada'
                   }">
                    <span class="notif-item__icon">
                        <i class="bi" :class="n.icona || 'bi-bell'" aria-hidden="true"></i>
                    </span>
                    <div class="notif-item__body">
                        <div class="notif-item__title" x-text="n.titol"></div>
                        <template x-if="n.missatge">
                            <div class="notif-item__msg" x-text="n.missatge"></div>
                        </template>
                        <div class="notif-item__time" x-text="n.creat"></div>
                    </div>
                </a>
            </template>
        </div>

        <div class="notif-bell-footer">
            <a :href="indexUrl">Veure totes les notificacions</a>
        </div>
    </div>
</div>

<script>
    (function () {
        function register() {
            if (window.__notificationBellRegistered) return;
            window.__notificationBellRegistered = true;

            window.Alpine.data('notificationBell', (cfg) => ({
                open: false, unreadCount: 0, items: [], timer: null, cfg,

                init() {
                    this.fetchData();
                    this.timer = setInterval(() => this.fetchData(), this.cfg.intervalMs);
                    document.addEventListener('visibilitychange', () => {
                        if (!document.hidden) this.fetchData();
                    });
                },

                toggle() {
                    this.open = !this.open;
                    if (this.open) this.fetchData();
                },

                async fetchData() {
                    try {
                        const res = await fetch(this.cfg.pollUrl, {
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                            credentials: 'same-origin'
                        });
                        if (!res.ok) return;
                        const data = await res.json();
                        this.unreadCount = data.unread_count || 0;
                        this.items = Array.isArray(data.items) ? data.items : [];
                    } catch (e) {}
                },

                async markAllRead() {
                    if (this.unreadCount === 0) return;
                    try {
                        await fetch(this.cfg.readAllUrl, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.cfg.csrf,
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            credentials: 'same-origin'
                        });
                        this.items = this.items.map(n => ({ ...n, llegida: true }));
                        this.unreadCount = 0;
                    } catch (e) {}
                },

                async markRead(id) {
                    try {
                        await fetch(this.cfg.readUrlBase + '/' + id + '/read', {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.cfg.csrf,
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            credentials: 'same-origin'
                        });
                    } catch (e) {}
                },

                onItemClick(ev, n) {
                    if (!n.llegida) {
                        n.llegida = true;
                        if (this.unreadCount > 0) this.unreadCount -= 1;
                        this.markRead(n.id);
                    }
                    if (!n.url) {
                        ev.preventDefault();
                        this.open = false;
                    }
                }
            }));
        }

        if (window.Alpine) register();
        else document.addEventListener('alpine:init', register);
    })();
</script>

@endif