@auth
    <script>
        (function () {
            var userId = '{{ auth()->id() }}';
            var keys = {
                user: 'superapp.active.user',
                tab: 'superapp.active.tab',
                heartbeat: 'superapp.active.heartbeat',
                sessionTab: 'superapp.session.tab'
            };
            var currentTab = sessionStorage.getItem(keys.sessionTab);
            var activeUser = localStorage.getItem(keys.user);
            var activeTab = localStorage.getItem(keys.tab);
            var lastHeartbeat = parseInt(localStorage.getItem(keys.heartbeat) || '0', 10);
            var hasFreshActiveTab = activeUser === userId && activeTab && Date.now() - lastHeartbeat < 30000;

            function makeTabId() {
                if (window.crypto && typeof window.crypto.randomUUID === 'function') {
                    return window.crypto.randomUUID();
                }

                return String(Date.now()) + '-' + String(Math.random()).slice(2);
            }

            function logoutForNewTab() {
                var form = document.createElement('form');
                var token = document.createElement('input');

                form.method = 'POST';
                form.action = '{{ route('logout') }}';
                form.style.display = 'none';

                token.type = 'hidden';
                token.name = '_token';
                token.value = '{{ csrf_token() }}';

                form.appendChild(token);
                document.body.appendChild(form);
                form.submit();
            }

            if (! currentTab && hasFreshActiveTab) {
                logoutForNewTab();
                return;
            }

            currentTab = currentTab || makeTabId();
            sessionStorage.setItem(keys.sessionTab, currentTab);

            function refreshActiveTab() {
                if (document.visibilityState === 'hidden') {
                    return;
                }

                localStorage.setItem(keys.user, userId);
                localStorage.setItem(keys.tab, currentTab);
                localStorage.setItem(keys.heartbeat, String(Date.now()));
            }

            refreshActiveTab();
            setInterval(refreshActiveTab, 5000);
            document.addEventListener('visibilitychange', refreshActiveTab);
        })();
    </script>
@endauth
