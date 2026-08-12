<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- FontAwesome CDN --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    @if ($businessProfile && $businessProfile->logo)
        <link rel="icon" type="image/png" href="{{ Storage::url($businessProfile->logo) }}">
    @endif
    <title>{{ $businessProfile ? $businessProfile->name : config('app.name') }}</title>
    <style>
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(68, 68, 68, 0.451);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 999;
            display: none;
        }
    </style>
    {{-- Simple-DataTables CDN --}}
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" type="text/css">
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" type="text/javascript"></script>

    {{-- Tippy.js CDN for Tooltips --}}
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://unpkg.com/tippy.js@6"></script>

    <script>
        window.setAppearance = function(appearance) {
            let setDark = () => document.documentElement.classList.add('dark')
            let setLight = () => document.documentElement.classList.remove('dark')
            let setButtons = (appearance) => {
                document.querySelectorAll('button[onclick^="setAppearance"]').forEach((button) => {
                    button.setAttribute('aria-pressed', String(appearance === button.value))
                })
            }
            if (appearance === 'system') {
                let media = window.matchMedia('(prefers-color-scheme: dark)')
                window.localStorage.removeItem('appearance')
                media.matches ? setDark() : setLight()
            } else if (appearance === 'dark') {
                window.localStorage.setItem('appearance', 'dark')
                setDark()
            } else if (appearance === 'light') {
                window.localStorage.setItem('appearance', 'light')
                setLight()
            }
            if (document.readyState === 'complete') {
                setButtons(appearance)
            } else {
                document.addEventListener("DOMContentLoaded", () => setButtons(appearance))
            }
        }
        window.setAppearance(window.localStorage.getItem('appearance') || 'system')
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200 antialiased" x-data="{
    sidebarOpen: localStorage.getItem('sidebarOpen') === null ? window.innerWidth >= 1024 : localStorage.getItem('sidebarOpen') === 'true',
    toggleSidebar() {
        this.sidebarOpen = !this.sidebarOpen;
        localStorage.setItem('sidebarOpen', this.sidebarOpen);
    },
    temporarilyOpenSidebar() {
        if (!this.sidebarOpen) {
            this.sidebarOpen = true;
            localStorage.setItem('sidebarOpen', true);
        }
    },
    formSubmitted: false,
}">

    <!-- Main Container -->
    <div class="min-h-screen flex flex-col">

        <x-layouts.app.header />

        <!-- Main Content Area -->
        <div class="flex flex-1 overflow-hidden">

            <x-layouts.app.sidebar />

            <!-- Main Content -->
            <main class="flex-1 overflow-auto bg-gray-100 dark:bg-gray-900 content-transition">
                <div class="p-6">
                    <!-- Success Message -->
                    @session('status')
                        <div x-data="{ showStatusMessage: true }" x-show="showStatusMessage" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform -translate-y-2"
                            class="mb-6 bg-green-50 dark:bg-green-900 border-l-4 border-green-500 p-4 rounded-md">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-500 dark:text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-green-700 dark:text-green-200">{{ session('status') }}</p>
                                </div>
                                <div class="ml-auto pl-3">
                                    <div class="-mx-1.5 -my-1.5">
                                        <button @click="showStatusMessage = false" class="inline-flex rounded-md p-1.5 text-green-500 dark:text-green-400 hover:bg-green-100 dark:hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                            <span class="sr-only">{{ __('Dismiss') }}</span>
                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endsession

                    {{ $slot }}

                </div>
            </main>
        </div>
    </div>
    <div class="loading-overlay" id="loadingOverlay">
        <svg width="64" height="64" viewBox="0 0 135 135" xmlns="http://www.w3.org/2000/svg" fill="#4361ee">
            <path d="M67.447 58c5.523 0 10-4.477 10-10s-4.477-10-10-10-10 4.477-10 10 4.477 10 10 10zm9.448 9.447c0 5.523 4.477 10 10 10 5.522 0 10-4.477 10-10s-4.478-10-10-10c-5.523 0-10 4.477-10 10zm-9.448 9.448c-5.523 0-10 4.477-10 10 0 5.522 4.477 10 10 10s10-4.478 10-10c0-5.523-4.477-10-10-10zM58 67.447c0-5.523-4.477-10-10-10s-10 4.477-10 10 4.477 10 10 10 10-4.477 10-10z">
                <animateTransform attributeName="transform" type="rotate" from="0 67 67" to="-360 67 67" dur="2.5s" repeatCount="indefinite" />
            </path>
            <path
                d="M28.19 40.31c6.627 0 12-5.374 12-12 0-6.628-5.373-12-12-12-6.628 0-12 5.372-12 12 0 6.626 5.372 12 12 12zm30.72-19.825c4.686 4.687 12.284 4.687 16.97 0 4.686-4.686 4.686-12.284 0-16.97-4.686-4.687-12.284-4.687-16.97 0-4.687 4.686-4.687 12.284 0 16.97zm35.74 7.705c0 6.627 5.37 12 12 12 6.626 0 12-5.373 12-12 0-6.628-5.374-12-12-12-6.63 0-12 5.372-12 12zm19.822 30.72c-4.686 4.686-4.686 12.284 0 16.97 4.687 4.686 12.285 4.686 16.97 0 4.687-4.686 4.687-12.284 0-16.97-4.685-4.687-12.283-4.687-16.97 0zm-7.704 35.74c-6.627 0-12 5.37-12 12 0 6.626 5.373 12 12 12s12-5.374 12-12c0-6.63-5.373-12-12-12zm-30.72 19.822c-4.686-4.686-12.284-4.686-16.97 0-4.686 4.687-4.686 12.285 0 16.97 4.686 4.687 12.284 4.687 16.97 0 4.687-4.685 4.687-12.283 0-16.97zm-35.74-7.704c0-6.627-5.372-12-12-12-6.626 0-12 5.373-12 12s5.374 12 12 12c6.628 0 12-5.373 12-12zm-19.823-30.72c4.687-4.686 4.687-12.284 0-16.97-4.686-4.686-12.284-4.686-16.97 0-4.687 4.686-4.687 12.284 0 16.97 4.686 4.687 12.284 4.687 16.97 0z">
                <animateTransform attributeName="transform" type="rotate" from="0 67 67" to="360 67 67" dur="8s" repeatCount="indefinite" />
            </path>
        </svg>
    </div>

    <!-- ✅ SweetAlert2 (CDN) -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener("alpine:init", () => {
            // This function replaces the underscores with spaces and then capitalize the first letter of each word
            Alpine.magic('ucwords', () => (text) => {
                if (!text) return '';
                return text
                    .replace(/_/g, ' ') // Replace underscores with spaces
                    .toLowerCase()
                    .replace(/\b[a-z]/g, (letter) => letter.toUpperCase()); // Capitalize the first letter of each word
            });
        });

        // Global submit function to submit form data
        async function submitFormData(url, formData, event = null) {
            if (event) {
                event.preventDefault();
            }
            const overlay = document.getElementById('loadingOverlay');

            try {
                overlay.style.display = 'flex'; // Show overlay

                const response = await fetch(url, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                });

                const data = await response.json();

                if (data.success) {
                    toast('success', data.message ?? 'Success!');
                    setTimeout(() => {
                        window.location.href = data.data.redirect;
                    }, 1000);
                } else {
                    if (data.errors) {
                        const errorMessages = Object.values(data.errors).flat();
                        errorMessages.forEach(message => {
                            toast('danger', message);
                        });
                    } else {
                        toast('danger', data.message || 'An error occurred');
                    }
                }

                return data;
            } catch (error) {
                toast('danger', 'An unexpected error occurred. Please try again.');
                throw error;
            } finally {
                overlay.style.display = 'none'; // Ensure overlay is always hidden
            }
        }
        document.addEventListener("DOMContentLoaded", function() {
            // ✅ Detects system or saved appearance
            const isDarkMode = () => {
                const stored = localStorage.getItem('appearance');
                if (stored === 'dark') return true;
                if (stored === 'light') return false;
                // Default to system preference
                return window.matchMedia('(prefers-color-scheme: dark)').matches;
            };

            // ✅ Toast function with automatic theme adaptation
            window.toast = function(type = 'success', message = 'Example toast text') {
                const dark = isDarkMode();

                const isSuccess = type === 'success';
                const isInfo = type === 'info';
                const isDanger = type === 'danger';

                let bgColor, textColor, iconColor, iconHtml;

                if (isInfo) {
                    bgColor = dark ? '#1e3a5f' : '#eff6ff';
                    textColor = dark ? '#93c5fd' : '#1e40af';
                    iconColor = dark ? '#60a5fa' : '#3b82f6';
                    iconHtml = `<i class="fa-regular fa-circle-info fa-beat-fade mr-2" style="color:${iconColor}"></i>`;
                } else {
                    bgColor = dark ?
                        (isSuccess ? '#064e3b' : '#7f1d1d') :
                        (isSuccess ? '#ecfdf5' : '#fef2f2');
                    textColor = dark ?
                        (isSuccess ? '#bbf7d0' : '#fecaca') :
                        (isSuccess ? '#166534' : '#991b1b');
                    iconColor = dark ?
                        (isSuccess ? '#4ade80' : '#f87171') :
                        (isSuccess ? '#16a34a' : '#dc2626');
                    iconHtml = isSuccess ?
                        `<i class="fa-regular fa-circle-check fa-beat-fade mr-2" style="color:${iconColor}"></i>` :
                        `<i class="fa-regular fa-circle-xmark fa-beat-fade mr-2" style="color:${iconColor}"></i>`;
                }

                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    html: iconHtml + `<span class="font-medium">${message}</span>`,
                    showConfirmButton: false,
                    timer: 4000,
                    timerProgressBar: true,
                    showCloseButton: true,
                    background: bgColor,
                    color: textColor,
                    width: 'auto',
                    customClass: {
                        popup: 'swal2-toast-custom',
                        container: 'swal2-top-end-custom'
                    },
                    didOpen: (toastEl) => {
                        toastEl.addEventListener('mouseenter', Swal.stopTimer);
                        toastEl.addEventListener('mouseleave', Swal.resumeTimer);
                    }
                });
            };
        });
    </script>

    <!-- ToolTip -->
    <script>
        document.addEventListener("alpine:init", () => {
            Alpine.directive("tooltip", (el, {
                expression
            }) => {
                tippy(el, {
                    content: expression,
                    placement: el.getAttribute("data-placement") || undefined,
                    allowHTML: true,
                    delay: el.getAttribute("data-delay") || 0,
                    animation: el.getAttribute("data-animation") || "fade",
                    theme: el.getAttribute("data-theme") || "",
                });
            });

            Alpine.magic("tooltip", (el) => (message, placement) => {
                let instance = tippy(el, {
                    content: message,
                    trigger: "manual",
                    placement: placement || undefined,
                    allowHTML: true,
                });

                instance.show();
            });
        });
    </script>
</body>

</html>
