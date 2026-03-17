<!DOCTYPE html>
<html lang="zxx" class="js">
@include('layouts.backLayout.head')

<body class="nk-body bg-lighter npc-default has-sidebar">
    <!-- Loader Overlay -->
    <!-- Loader Overlay -->
    <div id="global-loader" style="display:none;">
        <div class="loader">
            <div class="dot dot1"></div>
            <div class="dot dot2"></div>
        </div>
        <div class="loader-text">Loading...</div>
    </div>

    <div class="nk-app-root">
        <div class="nk-main">
            @include('layouts.backLayout.sidebar')
            <div class="nk-wrap">
                @include('layouts.backLayout.header')
                <div class="nk-content">
                    @yield('content')
                </div>
                @include('layouts.backLayout.footer')
            </div>
        </div>
    </div>
    @include('layouts.backLayout.scripts')
    <script>
        // Function that toggles status between active and inactive 
        function bindToggleStatus(className = '.eg-swal-av3', route = null) {
            $(document).on('click', className, function(e) {
                e.preventDefault();
                let recordId = $(this).data('id');
                Swal.fire({
                    title: 'Are you sure?',
                    text: `Change status?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, change it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: route || "/toggle-status",
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: {
                                id: recordId,
                            },
                            success: function(data) {
                                if (data.status === 'success') {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Updated!',
                                        text: data.message,
                                        timer: 1500,
                                        showConfirmButton: false
                                    }).then(() => {
                                        window.location.reload();
                                    });
                                } else {
                                    Swal.fire('Error!', data.message, 'error');
                                }
                            },
                            error: function() {
                                Swal.fire('Error!', 'Something went wrong.', 'error');
                            }
                        });
                    }
                });
            });
        }


        // Toaster that toast on top right side for each session messages 
        const hasMessage = @json(Session::has('message'));
        const message = @json(Session::get('message'));
        const type = @json(Session::get('type'));

        $(document).ready(function() {
            if (hasMessage && type && message) {
                toastr.clear();
                NioApp.Toast(message, type, {
                    position: 'top-right'
                });
            }
        });
    </script>

    {{--  for loader --}}
    <script>
        const loader = document.getElementById('global-loader');
        let loaderCount = 0;
        let autoHideTimer = null;
        const AUTO_HIDE_MS = 500;

        function showLoader() {
            loaderCount++;
            if (loader.style.display !== 'flex') loader.style.display = 'flex';
            // reset auto-hide timer
            if (autoHideTimer) clearTimeout(autoHideTimer);
            autoHideTimer = setTimeout(() => {
                console.warn('Loader auto-hidden after timeout (' + AUTO_HIDE_MS + 'ms)');
                loaderCount = 0;
                hideLoader(true);
            }, AUTO_HIDE_MS);
        }

        function hideLoader(force = false) {
            if (force) {
                loaderCount = 0;
            } else {
                loaderCount = Math.max(0, loaderCount - 1);
            }
            if (loaderCount === 0) {
                loader.style.display = 'none';
                if (autoHideTimer) {
                    clearTimeout(autoHideTimer);
                    autoHideTimer = null;
                }
            }
        }

        document.addEventListener('submit', function(e) {
            const form = e.target;

            const submitter = e.submitter || form.querySelector('[type="submit"]');
            if (submitter && (submitter.matches('.no-loader') || submitter.dataset.noLoader !== undefined)) return;
            if (form.dataset.noLoader !== undefined || form.classList.contains('no-loader')) return;

            const action = form.getAttribute('action');
            if (!action || action.trim() === '' || action.trim() === '#' || action.trim().startsWith(
                    'javascript:')) {
                return;
            }
            setTimeout(() => {
                if (!e.defaultPrevented) {
                    showLoader();
                }
            }, 0);
        }, true);

        document.addEventListener('click', function(e) {
            const clickable = e.target.closest('a, button');
            if (!clickable) return;

            if (clickable.dataset.noLoader !== undefined || clickable.classList.contains('no-loader')) return;

            if (clickable.hasAttribute('data-bs-toggle') || clickable.hasAttribute('data-toggle') || clickable
                .closest('.swal2-container')) return;

            const tag = clickable.tagName.toLowerCase();

            if (tag === 'a') {
                const href = clickable.getAttribute('href');
                if (!href || href === '#' || href.startsWith('javascript:') || href.startsWith('mailto:') || href
                    .startsWith('tel:')) return;
                try {
                    const url = new URL(href, location.href);
                    if (url.pathname === location.pathname && url.search === location.search && url.hash) {
                        return;
                    }
                } catch (err) {}
                if (clickable.target === '_blank') return;
            } else if (tag === 'button') {
                const type = (clickable.getAttribute('type') || '').toLowerCase();
                if (type === 'button') return;
                if (type === 'submit') return;
            }
            showLoader();
        }, true);
        if (window.jQuery) {
            $(document).ajaxStart(function() {
                showLoader();
            }).ajaxStop(function() {
                hideLoader();
            });
        }
        window.addEventListener('load', () => hideLoader(true));
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) hideLoader(true);
        });
    </script>
    @stack('scripts')
</body>

</html>
