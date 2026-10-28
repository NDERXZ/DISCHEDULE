/**
 * Dischedule Real-time In-App Notification Center
 */

window.DischeduleNotifications = {
  unreadCount: 0,
  pollInterval: 30000, // 30 seconds

  init() {
    this.fetchUnread();
    setInterval(() => this.fetchUnread(), this.pollInterval);

    const bellBtn = document.getElementById('notif-bell-btn');
    if (bellBtn) {
      bellBtn.addEventListener('click', () => {
        this.fetchUnread();
      });
    }
  },

  async fetchUnread() {
    const res = await Dischedule.fetch('/notifications/unread-list');
    if (res.ok && res.data) {
      this.unreadCount = res.data.unread_count || 0;
      this.updateBadge();
      this.renderList(res.data.notifications || []);
    }
  },

  updateBadge() {
    const badge = document.getElementById('notif-badge');
    if (!badge) return;

    if (this.unreadCount > 0) {
      badge.textContent = this.unreadCount > 99 ? '99+' : this.unreadCount;
      badge.style.display = 'inline-flex';
    } else {
      badge.style.display = 'none';
    }
  },

  renderList(notifications) {
    const listContainer = document.getElementById('notif-dropdown-list');
    if (!listContainer) return;

    if (notifications.length === 0) {
      listContainer.innerHTML = `
        <div style="padding:24px;text-align:center;color:var(--text-muted);font-size:13px;">
          <i class="fa-regular fa-bell-slash" style="font-size:24px;margin-bottom:8px;display:block;opacity:0.5;"></i>
          No unread notifications.
        </div>
      `;
      return;
    }

    const typeIcons = {
      info: 'fa-solid fa-circle-info text-info',
      success: 'fa-solid fa-circle-check text-success',
      warning: 'fa-solid fa-triangle-exclamation text-warning',
      danger: 'fa-solid fa-circle-xmark text-danger'
    };

    listContainer.innerHTML = notifications.map(n => `
      <div style="padding:12px 16px;border-bottom:1px solid var(--border-color);display:flex;gap:12px;background:${n.is_read ? 'transparent' : 'var(--bg-subtle)'};transition:background 0.2s;">
        <i class="${typeIcons[n.type] || 'fa-solid fa-bell'}" style="margin-top:2px;font-size:14px;"></i>
        <div style="flex:1;">
          <div style="font-weight:600;font-size:13px;color:var(--text-main);">${n.title}</div>
          <div style="font-size:12px;color:var(--text-muted);margin:2px 0;">${n.message}</div>
          <div style="display:flex;align-items:center;justify-content:space-between;margin-top:4px;">
            <span style="font-size:10.5px;color:var(--text-subtle);">${n.created_at}</span>
            ${n.link ? `<a href="${n.link}" style="font-size:11.5px;font-weight:600;">View →</a>` : ''}
          </div>
        </div>
        ${!n.is_read ? `
          <button onclick="DischeduleNotifications.markSingleRead(${n.id})" title="Mark as read" style="background:none;border:none;color:var(--text-subtle);cursor:pointer;">
            <i class="fa-solid fa-check"></i>
          </button>
        ` : ''}
      </div>
    `).join('');
  },

  async markSingleRead(id) {
    const res = await Dischedule.fetch(`/notifications/${id}/read`, { method: 'POST' });
    if (res.ok) {
      this.fetchUnread();
    }
  },

  async markAllRead() {
    const res = await Dischedule.fetch('/notifications/mark-all-read', { method: 'POST' });
    if (res.ok) {
      this.unreadCount = 0;
      this.updateBadge();
      this.fetchUnread();
      Dischedule.toast('success', 'All notifications marked as read.');
    }
  },

  toggleDropdown() {
    const dropdown = document.getElementById('notif-dropdown-menu');
    if (dropdown) {
      dropdown.classList.toggle('show');
    }
  }
};

document.addEventListener('DOMContentLoaded', () => {
  DischeduleNotifications.init();
});
