/**
 * Dischedule Application Core JavaScript
 */

window.Dischedule = {
  csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',

  getCsrfToken() {
    if (!this.csrfToken) {
      this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    }
    return this.csrfToken;
  },

  init() {
    this.initTheme();
    this.initKeyboardShortcuts();
    this.initDropdowns();
    this.initModals();
  },

  // --- Theme Management ---
  initTheme() {
    const saved = localStorage.getItem('dischedule_theme') || 'system';
    this.applyTheme(saved);
  },

  applyTheme(theme) {
    let effective = theme;
    if (theme === 'system') {
      const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
      effective = prefersDark ? 'dark' : 'light';
    }
    document.documentElement.setAttribute('data-theme', effective);
    localStorage.setItem('dischedule_theme', theme);

    // Toggle icons
    document.querySelectorAll('.theme-icon-moon').forEach(el => {
      el.style.display = effective === 'dark' ? 'none' : 'inline-block';
    });
    document.querySelectorAll('.theme-icon-sun').forEach(el => {
      el.style.display = effective === 'dark' ? 'inline-block' : 'none';
    });
  },

  toggleTheme() {
    const current = document.documentElement.getAttribute('data-theme') || 'light';
    const next = current === 'dark' ? 'light' : 'dark';
    
    document.body.classList.add('theme-transitioning');
    this.applyTheme(next);
    setTimeout(() => {
      document.body.classList.remove('theme-transitioning');
    }, 300);

    this.toast('info', `Switched to ${next} mode.`);
  },

  // --- Toast Engine ---
  toast(type, message, duration = 4000) {
    let container = document.getElementById('toast-container');
    if (!container) {
      container = document.createElement('div');
      container.id = 'toast-container';
      container.className = 'toast-container';
      document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;

    const icons = {
      success: 'fa-solid fa-circle-check',
      error: 'fa-solid fa-circle-xmark',
      warning: 'fa-solid fa-triangle-exclamation',
      info: 'fa-solid fa-circle-info'
    };

    toast.innerHTML = `
      <i class="${icons[type] || icons.info}" style="margin-top:2px; font-size:16px;"></i>
      <div style="flex:1;">
        <div style="font-weight:600; text-transform:capitalize; margin-bottom:2px;">${type}</div>
        <div style="font-size:13px; color:var(--text-muted);">${message}</div>
      </div>
      <button onclick="this.parentElement.remove()" style="background:none;border:none;color:var(--text-subtle);cursor:pointer;">
        <i class="fa-solid fa-xmark"></i>
      </button>
    `;

    container.appendChild(toast);
    setTimeout(() => toast.classList.add('show'), 10);

    setTimeout(() => {
      toast.classList.remove('show');
      setTimeout(() => toast.remove(), 300);
    }, duration);
  },

  // --- Modal Engine ---
  initModals() {
    document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
      backdrop.addEventListener('click', (e) => {
        if (e.target === backdrop) {
          backdrop.classList.remove('active');
          document.body.style.overflow = '';
        }
      });
    });
  },

  openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
      modal.classList.add('active');
      document.body.style.overflow = 'hidden';
    }
  },

  closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
      modal.classList.remove('active');
      document.body.style.overflow = '';
    }
  },

  // --- CSRF Fetch Helper ---
  async fetch(url, options = {}) {
    options.headers = options.headers || {};
    options.headers['X-Requested-With'] = 'XMLHttpRequest';
    options.headers['X-CSRF-TOKEN'] = this.getCsrfToken();

    if (options.body && typeof options.body === 'object' && !(options.body instanceof FormData)) {
      options.headers['Content-Type'] = 'application/json';
      options.body = JSON.stringify(options.body);
    }

    try {
      const res = await fetch(url, options);
      const data = await res.json();
      return { ok: res.ok, status: res.status, data };
    } catch (err) {
      console.error('Fetch error:', err);
      return { ok: false, status: 500, data: { success: false, message: 'Network request failed.' } };
    }
  },

  // --- Dropdown Management ---
  initDropdowns() {
    document.addEventListener('click', (e) => {
      const toggle = e.target.closest('[data-toggle="dropdown"]');
      const clickedInsideDropdown = e.target.closest('.dropdown-menu');

      if (toggle) {
        e.preventDefault();
        e.stopPropagation();

        const parent = toggle.closest('.dropdown') || toggle.parentElement;
        const menu = parent ? parent.querySelector('.dropdown-menu') : toggle.nextElementSibling;
        const isCurrentlyShown = menu && menu.classList.contains('show');

        // Close all other dropdowns
        document.querySelectorAll('.dropdown-menu.show').forEach(d => {
          d.classList.remove('show');
        });

        if (menu && !isCurrentlyShown) {
          menu.classList.add('show');
        }
        return;
      }

      // If clicked inside dropdown on a link or button, close dropdown after action
      if (clickedInsideDropdown) {
        if (e.target.closest('a') || e.target.closest('button')) {
          clickedInsideDropdown.classList.remove('show');
        }
        return;
      }

      // Clicked outside
      document.querySelectorAll('.dropdown-menu.show').forEach(d => {
        d.classList.remove('show');
      });
    });
  },

  // --- Keyboard Shortcuts ---
  initKeyboardShortcuts() {
    document.addEventListener('keydown', (e) => {
      // Ignore when user is actively typing in form inputs
      if (['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement.tagName) || document.activeElement.isContentEditable) {
        return;
      }

      const key = e.key.toUpperCase();

      if (e.key === '/') {
        e.preventDefault();
        const search = document.getElementById('global-search');
        if (search) search.focus();
      } else if (key === 'N') {
        e.preventDefault();
        window.location.href = '/events/create';
      } else if (key === 'T') {
        if (window.DischeduleCalendar) window.DischeduleCalendar.today();
      } else if (key === 'M') {
        if (window.DischeduleCalendar) window.DischeduleCalendar.switchView('month');
      } else if (key === 'W') {
        if (window.DischeduleCalendar) window.DischeduleCalendar.switchView('week');
      } else if (key === 'D') {
        if (window.DischeduleCalendar) window.DischeduleCalendar.switchView('day');
      } else if (key === 'A') {
        if (window.DischeduleCalendar) window.DischeduleCalendar.switchView('agenda');
      }
    });
  },

  toggleSidebar() {
    const sidebar = document.getElementById('app-sidebar');
    if (!sidebar) return;
    sidebar.classList.toggle('open');

    let backdrop = document.getElementById('sidebar-backdrop');
    if (!backdrop) {
      backdrop = document.createElement('div');
      backdrop.id = 'sidebar-backdrop';
      backdrop.className = 'sidebar-backdrop';
      backdrop.onclick = () => this.toggleSidebar();
      document.body.appendChild(backdrop);
    }
    backdrop.classList.toggle('active', sidebar.classList.contains('open'));
  },

  copyToClipboard(text, successMsg = 'Copied to clipboard!') {
    navigator.clipboard.writeText(text).then(() => {
      this.toast('success', successMsg);
    }).catch(() => {
      this.toast('error', 'Failed to copy to clipboard.');
    });
  }
};

document.addEventListener('DOMContentLoaded', () => {
  Dischedule.init();
});
