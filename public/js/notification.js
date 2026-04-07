// notification.js - Fixed version with proper auto-close
(function () {
    'use strict';

    class NotificationSystem {
        constructor() {
            this.container = null;
            this.notificationCount = 0;
            this.maxNotifications = 5;
            this.timeouts = new Map(); // Store timeouts for proper cleanup
            this.init();
        }

        init() {
            // Create notification container if it doesn't exist
            let container = document.querySelector('#notification-container');
            if (!container) {
                this.container = document.createElement('div');
                this.container.id = 'notification-container';
                this.container.className = 'fixed top-0 right-0 z-50 max-w-sm w-full p-4 space-y-4 pointer-events-none';
                document.body.appendChild(this.container);
                this.addStyles();
            } else {
                this.container = container;
            }
        }

        addStyles() {
            if (document.getElementById('notification-styles')) return;

            const style = document.createElement('style');
            style.id = 'notification-styles';
            style.textContent = `
                #notification-container {
                    pointer-events: none;
                }
                
                #notification-container > div {
                    pointer-events: auto;
                }
                
                @keyframes slideIn {
                    from {
                        transform: translateX(100%);
                        opacity: 0;
                    }
                    to {
                        transform: translateX(0);
                        opacity: 1;
                    }
                }
                
                @keyframes slideOut {
                    from {
                        transform: translateX(0);
                        opacity: 1;
                    }
                    to {
                        transform: translateX(100%);
                        opacity: 0;
                    }
                }
                
                @keyframes progress {
                    from { width: 100%; }
                    to { width: 0%; }
                }
                
                .notification-item {
                    animation: slideIn 0.3s ease-out forwards;
                }
                
                .notification-slide-out {
                    animation: slideOut 0.3s ease-in forwards;
                }
                
                .notification-progress-bar {
                    position: absolute;
                    bottom: 0;
                    left: 0;
                    height: 3px;
                    border-radius: 0 0 0 0.75rem;
                    width: 100%;
                }
                
                .notification-progress-success {
                    animation: progress 5s linear forwards;
                    background: linear-gradient(to right, #10b981, #34d399);
                }
                
                .notification-progress-error {
                    animation: progress 8s linear forwards;
                    background: linear-gradient(to right, #f43f5e, #fb7185);
                }
                
                .notification-progress-warning {
                    animation: progress 6s linear forwards;
                    background: linear-gradient(to right, #f59e0b, #fbbf24);
                }
                
                .notification-progress-info {
                    animation: progress 4s linear forwards;
                    background: linear-gradient(to right, #3b82f6, #60a5fa);
                }
            `;
            document.head.appendChild(style);
        }

        show(message, type = 'info', title = null, duration = null) {
            // Ensure container exists
            if (!this.container) this.init();

            // Set defaults
            if (!title) {
                switch (type) {
                    case 'success': title = 'Success!'; break;
                    case 'error': title = 'Error!'; break;
                    case 'warning': title = 'Warning!'; break;
                    case 'info': title = 'Information'; break;
                    default: title = 'Notification';
                }
            }

            if (!duration) {
                switch (type) {
                    case 'success': duration = 5000; break;
                    case 'error': duration = 8000; break;
                    case 'warning': duration = 6000; break;
                    case 'info': duration = 4000; break;
                    default: duration = 5000;
                }
            }

            // Create unique ID
            const notificationId = 'notification-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);

            // Create notification element
            const notification = document.createElement('div');
            notification.id = notificationId;
            notification.className = 'notification-item';

            // Set gradient
            const gradient = this.getGradient(type);
            notification.style.background = gradient;

            // Build HTML
            notification.innerHTML = `
                <div class="rounded-xl shadow-lg overflow-hidden text-white relative">
                    <div class="p-4 flex items-start">
                        <div class="flex-shrink-0">
                            ${this.getIcon(type)}
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="font-semibold">${title}</p>
                            <p class="text-sm opacity-90">${this.escapeHtml(message)}</p>
                        </div>
                        <button type="button" 
                                class="ml-4 text-white hover:opacity-80 focus:outline-none transition-opacity close-button">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="notification-progress-bar ${this.getProgressClass(type)}"></div>
                </div>
            `;

            // Limit notifications
            if (this.notificationCount >= this.maxNotifications) {
                const oldest = this.container.children[0];
                if (oldest) this.remove(oldest.id);
            }

            // Add to container
            this.container.appendChild(notification);
            this.notificationCount++;

            // Add click handler for close button
            const closeBtn = notification.querySelector('.close-button');
            closeBtn.addEventListener('click', () => {
                this.remove(notificationId);
            });

            // Setup auto-remove with proper timeout tracking
            const timeoutId = setTimeout(() => {
                this.remove(notificationId);
            }, duration);

            // Store timeout reference
            this.timeouts.set(notificationId, timeoutId);

            // Handle animation end
            notification.addEventListener('animationend', (e) => {
                if (e.animationName === 'slideOut') {
                    this.cleanup(notificationId);
                }
            });

            return notificationId;
        }

        remove(id) {
            const notification = document.getElementById(id);
            if (notification) {
                // Clear the timeout
                if (this.timeouts.has(id)) {
                    clearTimeout(this.timeouts.get(id));
                    this.timeouts.delete(id);
                }

                // Add slide-out animation
                notification.classList.remove('notification-item');
                notification.classList.add('notification-slide-out');

                // Remove after animation completes
                setTimeout(() => {
                    this.cleanup(id);
                }, 300);
            }
        }

        cleanup(id) {
            const notification = document.getElementById(id);
            if (notification && notification.parentNode) {
                notification.parentNode.removeChild(notification);
                this.notificationCount--;
            }

            // Clean up timeout reference
            if (this.timeouts.has(id)) {
                this.timeouts.delete(id);
            }
        }

        clearAll() {
            // Clear all timeouts first
            this.timeouts.forEach((timeoutId, notificationId) => {
                clearTimeout(timeoutId);
            });
            this.timeouts.clear();

            // Remove all notifications
            const notifications = this.container.querySelectorAll('[id^="notification-"]');
            notifications.forEach(notification => {
                notification.classList.remove('notification-item');
                notification.classList.add('notification-slide-out');
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 300);
            });

            this.notificationCount = 0;
        }

        // Helper methods remain the same
        getGradient(type) {
            switch (type) {
                case 'success': return 'linear-gradient(to right, #10b981, #059669)';
                case 'error': return 'linear-gradient(to right, #ef4444, #dc2626)';
                case 'warning': return 'linear-gradient(to right, #f59e0b, #d97706)';
                case 'info': return 'linear-gradient(to right, #3b82f6, #2563eb)';
                default: return 'linear-gradient(to right, #6b7280, #4b5563)';
            }
        }

        getIcon(type) {
            const icons = {
                success: `<svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                          </svg>`,
                error: `<svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>`,
                warning: `<svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.998-.833-2.732 0L4.346 16.5c-.77.833.192 2.5 1.732 2.5z" />
                          </svg>`,
                info: `<svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                       </svg>`
            };
            return icons[type] || icons.info;
        }

        getProgressClass(type) {
            switch (type) {
                case 'success': return 'notification-progress-success';
                case 'error': return 'notification-progress-error';
                case 'warning': return 'notification-progress-warning';
                case 'info': return 'notification-progress-info';
                default: return 'notification-progress-info';
            }
        }

        escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    }

    // Initialize and expose
    if (!window.notificationSystem) {
        window.notificationSystem = new NotificationSystem();
    }

    // Global take-over from shim (Force overwrite to replace dummy console logs)
    window.Notification = {
        success: (msg, title, duration) => window.notificationSystem.show(msg, 'success', title, duration),
        error: (msg, title, duration) => window.notificationSystem.show(msg, 'error', title, duration),
        warning: (msg, title, duration) => window.notificationSystem.show(msg, 'warning', title, duration),
        info: (msg, title, duration) => window.notificationSystem.show(msg, 'info', title, duration)
    };

    // Global functions
    if (!window.showNotification) {
        window.showNotification = function (message, type = 'info', title = null, duration = null) {
            return window.notificationSystem.show(message, type, title, duration);
        };
    }

    if (!window.showSuccess) {
        window.showSuccess = function (message, title = 'Success!', duration = 5000) {
            return showNotification(message, 'success', title, duration);
        };
    }

    if (!window.showError) {
        window.showError = function (message, title = 'Error!', duration = 8000) {
            return showNotification(message, 'error', title, duration);
        };
    }

    if (!window.showWarning) {
        window.showWarning = function (message, title = 'Warning!', duration = 6000) {
            return showNotification(message, 'warning', title, duration);
        };
    }

    if (!window.showInfo) {
        window.showInfo = function (message, title = 'Information', duration = 4000) {
            return showNotification(message, 'info', title, duration);
        };
    }

    if (!window.clearNotifications) {
        window.clearNotifications = function () {
            if (window.notificationSystem) {
                window.notificationSystem.clearAll();
            }
        };
    }

    // Debug helper
    window.debugNotifications = function () {
        console.log('Active notifications:', window.notificationSystem.notificationCount);
        console.log('Active timeouts:', window.notificationSystem.timeouts.size);
    };
})();