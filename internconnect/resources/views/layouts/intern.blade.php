<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ROC.PH Intern Portal')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @yield('styles')
    <style>
        .notification-icon {
            position: fixed;
            top: 20px;
            right: 20px;
            cursor: pointer;
            z-index: 1000;
        }
        
        .notification-bell {
            font-size: 24px;
            position: relative;
        }
        
        .notification-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: #dc3545;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
        }
        
        .notification-dropdown {
            position: fixed;
            top: 70px;
            right: 20px;
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            width: 350px;
            max-height: 400px;
            overflow-y: auto;
            z-index: 999;
            display: none;
        }
        
        .notification-dropdown.show {
            display: block;
        }
        
        .notification-dropdown-header {
            padding: 12px 16px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 600;
        }
        
        .notification-item {
            padding: 12px 16px;
            border-bottom: 1px solid #eee;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        
        .notification-item:hover {
            background-color: #f5f5f5;
        }
        
        .notification-item.unread {
            background-color: #f0f8ff;
        }
        
        .notification-item-text {
            font-size: 14px;
            margin-bottom: 4px;
        }
        
        .notification-item-time {
            font-size: 12px;
            color: #999;
        }
        
        .notification-item-type {
            display: inline-block;
            font-size: 11px;
            padding: 2px 6px;
            border-radius: 3px;
            background-color: #e3f2fd;
            color: #1976d2;
            margin-right: 8px;
        }
        
        .notification-empty {
            padding: 24px 16px;
            text-align: center;
            color: #999;
        }
        
        .mark-all-read-btn {
            padding: 8px 12px;
            background-color: #0dcaf0;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
        }
        
        .mark-all-read-btn:hover {
            background-color: #0bb5e0;
        }
    </style>
</head>
<body>
<div class="app-container app">
    {{-- SIDEBAR --}}
    <aside class="sidebar">
        <div class="logo">
            <span class="badge">R</span>
            <div>
                <h2>ROC.PH</h2>
                <small>Intern Portal</small>
            </div>
        </div>

        <div class="user">
            <div class="avatar">{{ strtoupper(substr(Auth::user()->first_name, 0, 1) . substr(Auth::user()->last_name, 0, 1)) }}</div>
            <div>
                <strong>{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</strong>
                <small>{{ Auth::user()->user_role }}</small>
            </div>
        </div>

        <nav class="menu">
            <a class="{{ request()->routeIs('intern.dashboard') ? 'active' : '' }}" href="{{ route('intern.dashboard') }}">Home</a>
            <a class="{{ request()->routeIs('intern.job.search') ? 'active' : '' }}" href="{{ route('intern.job.search') }}">Job Search</a>
            <a class="{{ request()->routeIs('intern.profile') ? 'active' : '' }}" href="{{ route('intern.profile', auth()->id()) }}">Profile</a>
            <a class="logout" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </nav>
    </aside>

    {{-- NOTIFICATION ICON --}}
    <div class="notification-icon" id="notificationContainer">
        <div class="notification-bell" id="notificationBell">
            🔔
            <span class="notification-badge" id="notificationBadge" style="display: none;">0</span>
        </div>
        
        <div class="notification-dropdown" id="notificationDropdown">
            <div class="notification-dropdown-header">
                <span>Notifications</span>
                <button class="mark-all-read-btn" id="markAllReadBtn" style="display: none;">Mark all as read</button>
            </div>
            <div id="notificationList"></div>
        </div>
    </div>

    {{-- MAIN CONTENT --}}
    <main class="content main">
        @yield('content')
    </main>
</div>

<script>
    const notificationBell = document.getElementById('notificationBell');
    const notificationDropdown = document.getElementById('notificationDropdown');
    const notificationBadge = document.getElementById('notificationBadge');
    const notificationList = document.getElementById('notificationList');
    const markAllReadBtn = document.getElementById('markAllReadBtn');

    // Toggle dropdown
    notificationBell.addEventListener('click', function(e) {
        e.stopPropagation();
        notificationDropdown.classList.toggle('show');
        if (notificationDropdown.classList.contains('show')) {
            loadNotifications();
        }
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!document.getElementById('notificationContainer').contains(e.target)) {
            notificationDropdown.classList.remove('show');
        }
    });

    // Load notifications
    async function loadNotifications() {
        try {
            const response = await fetch('/api/notifications', {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Accept': 'application/json'
                }
            });
            
            const data = await response.json();
            console.log('Notification API Response:', data);
            
            if (data.success && data.data) {
                updateNotificationUI(data.data, data.unread_count);
            } else if (!response.ok) {
                console.error('API Error:', response.status, data);
            }
        } catch (error) {
            console.error('Error loading notifications:', error);
        }
    }

    // Update notification UI
    function updateNotificationUI(notifications, unreadCount) {
        // Update badge
        if (unreadCount > 0) {
            notificationBadge.textContent = unreadCount;
            notificationBadge.style.display = 'flex';
            markAllReadBtn.style.display = 'block';
        } else {
            notificationBadge.style.display = 'none';
            markAllReadBtn.style.display = 'none';
        }

        // Update list
        if (notifications.length === 0) {
            notificationList.innerHTML = '<div class="notification-empty">No notifications</div>';
            return;
        }

        notificationList.innerHTML = notifications.map(notif => `
            <div class="notification-item ${!notif.is_read ? 'unread' : ''}" onclick="handleNotificationClick(${notif.notification_id}, '${notif.action_url || '#'}')">
                <div style="display: flex; justify-content: space-between; align-items: start;">
                    <div style="flex: 1;">
                        <span class="notification-item-type">${notif.type}</span>
                        <div class="notification-item-text">${notif.message}</div>
                        <div class="notification-item-time">${new Date(notif.send_date).toLocaleString()}</div>
                    </div>
                    ${!notif.is_read ? '<div style="width: 8px; height: 8px; background-color: #0dcaf0; border-radius: 50%; margin-top: 4px;"></div>' : ''}
                </div>
            </div>
        `).join('');
    }

    // Handle notification click - mark as read and redirect
    async function handleNotificationClick(notificationId, actionUrl) {
        // Mark as read
        try {
            const response = await fetch(`/api/notifications/${notificationId}/read`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });

            if (response.ok) {
                // Close the dropdown
                notificationDropdown.classList.remove('show');
                // Redirect to action URL if it exists
                if (actionUrl && actionUrl !== '#') {
                    window.location.href = actionUrl;
                } else {
                    loadNotifications();
                }
            }
        } catch (error) {
            console.error('Error handling notification click:', error);
        }
    }

    // Mark single notification as read
    async function markNotificationRead(notificationId) {
        try {
            const response = await fetch(`/api/notifications/${notificationId}/read`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });

            if (response.ok) {
                loadNotifications();
            }
        } catch (error) {
            console.error('Error marking notification as read:', error);
        }
    }

    // Mark all as read
    document.getElementById('markAllReadBtn').addEventListener('click', async function(e) {
        e.stopPropagation();
        try {
            const response = await fetch('/api/notifications/read-all', {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Accept': 'application/json'
                }
            });

            if (response.ok) {
                loadNotifications();
            }
        } catch (error) {
            console.error('Error marking all as read:', error);
        }
    });

    // Load notifications on page load
    window.addEventListener('load', function() {
        loadNotifications();
        // Refresh every 30 seconds
        setInterval(loadNotifications, 30000);
    });
</script>
