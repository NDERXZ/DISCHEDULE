/**
 * Dischedule Interactive Calendar Engine
 * Compact Month View with Dots & Floating Popover Details, Week, Day, and Agenda
 */

window.DischeduleCalendar = {
  currentDate: new Date(),
  currentView: 'month', // 'month', 'week', 'day', 'agenda'
  events: [],
  filters: {
    category_id: '',
    priority: '',
    status: '',
    tag_id: '',
    search: ''
  },
  isLoading: false,
  selectedDate: null,
  popoverEl: null,
  hideTimer: null,
  isPinned: false,

  init(options = {}) {
    this.currentView = options.defaultView || 'month';
    this.initPopover();
    this.bindControls();
    this.loadEvents();
  },

  initPopover() {
    let popover = document.getElementById('calendar-popover');
    if (!popover) {
      popover = document.createElement('div');
      popover.id = 'calendar-popover';
      popover.className = 'calendar-popover';
      document.body.appendChild(popover);
    }
    this.popoverEl = popover;

    // Hover interactions for the popover itself
    this.popoverEl.addEventListener('mouseenter', () => {
      if (!this.isPinned) {
        this.cancelHidePopover();
      }
    });

    this.popoverEl.addEventListener('mouseleave', () => {
      if (!this.isPinned) {
        this.scheduleHidePopover(150);
      }
    });

    // Close on click outside or escape
    document.addEventListener('click', (e) => {
      if (!e.target.closest('.calendar-cell') && !e.target.closest('.calendar-popover') && !e.target.closest('.time-slot-line') && !e.target.closest('.time-day-col')) {
        this.hidePopover(true);
      }
    });

    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        this.hidePopover(true);
      }
    });
  },

  scheduleHidePopover(delay = 150) {
    if (this.isPinned) return;
    this.cancelHidePopover();
    this.hideTimer = setTimeout(() => {
      this.hidePopover(false);
    }, delay);
  },

  cancelHidePopover() {
    if (this.hideTimer) {
      clearTimeout(this.hideTimer);
      this.hideTimer = null;
    }
  },

  bindControls() {
    // View mode tab buttons (Month, Week, Day, Agenda)
    document.querySelectorAll('.view-tab').forEach(tab => {
      tab.addEventListener('click', (e) => {
        e.preventDefault();
        const btn = e.currentTarget;
        const view = btn.dataset.view;
        if (view) {
          this.switchView(view);
        }
      });
    });

    // Navigation buttons
    const prevBtn = document.getElementById('cal-prev');
    const nextBtn = document.getElementById('cal-next');
    const todayBtn = document.getElementById('cal-today');

    if (prevBtn) {
      prevBtn.addEventListener('click', (e) => {
        e.preventDefault();
        this.navigate(-1);
      });
    }

    if (nextBtn) {
      nextBtn.addEventListener('click', (e) => {
        e.preventDefault();
        this.navigate(1);
      });
    }

    if (todayBtn) {
      todayBtn.addEventListener('click', (e) => {
        e.preventDefault();
        this.today();
      });
    }

    // Filter dropdowns
    document.querySelectorAll('.calendar-filter').forEach(input => {
      input.addEventListener('change', () => {
        this.filters[input.name] = input.value;
        this.loadEvents();
      });
    });

    // Search input (debounced)
    const searchInput = document.getElementById('calendar-search');
    if (searchInput) {
      let debounceTimer;
      searchInput.addEventListener('input', (e) => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
          this.filters.search = e.target.value.trim();
          this.loadEvents();
        }, 300);
      });
    }
  },

  switchView(view) {
    if (!view) return;
    this.hidePopover();
    this.currentView = view;
    document.querySelectorAll('.view-tab').forEach(tab => {
      tab.classList.toggle('active', tab.dataset.view === view);
    });
    this.loadEvents();
  },

  navigate(direction) {
    this.hidePopover();
    const d = new Date(this.currentDate);
    if (this.currentView === 'month') {
      d.setDate(1);
      d.setMonth(d.getMonth() + direction);
    } else if (this.currentView === 'week') {
      d.setDate(d.getDate() + (direction * 7));
    } else if (this.currentView === 'day') {
      d.setDate(d.getDate() + direction);
    } else if (this.currentView === 'agenda') {
      d.setDate(1);
      d.setMonth(d.getMonth() + direction);
    }
    this.currentDate = d;
    this.loadEvents();
  },

  today() {
    this.hidePopover();
    this.currentDate = new Date();
    this.loadEvents();
  },

  async loadEvents() {
    if (this.isLoading) return;
    this.isLoading = true;

    const range = this.calculateDateRange();
    const query = new URLSearchParams({
      start: range.start,
      end: range.end,
      ...this.filters
    });

    this.renderLoading();

    try {
      const res = await Dischedule.fetch(`/calendar/feed?${query.toString()}`);
      if (res.ok && Array.isArray(res.data)) {
        this.events = res.data;
      } else {
        this.events = [];
      }
    } catch (err) {
      console.error('Failed to load events:', err);
      this.events = [];
    } finally {
      this.isLoading = false;
      this.render();
    }
  },

  calculateDateRange() {
    const year = this.currentDate.getFullYear();
    const month = this.currentDate.getMonth();

    if (this.currentView === 'month') {
      const firstDay = new Date(year, month, 1);
      const startDayOffset = (firstDay.getDay() + 6) % 7;
      firstDay.setDate(firstDay.getDate() - startDayOffset);

      const lastDay = new Date(firstDay);
      lastDay.setDate(lastDay.getDate() + 41);

      return {
        start: this.formatDate(firstDay) + ' 00:00:00',
        end: this.formatDate(lastDay) + ' 23:59:59'
      };
    } else if (this.currentView === 'week') {
      const start = new Date(this.currentDate);
      const dayOffset = (start.getDay() + 6) % 7;
      start.setDate(start.getDate() - dayOffset);

      const end = new Date(start);
      end.setDate(end.getDate() + 6);

      return {
        start: this.formatDate(start) + ' 00:00:00',
        end: this.formatDate(end) + ' 23:59:59'
      };
    } else if (this.currentView === 'day') {
      return {
        start: this.formatDate(this.currentDate) + ' 00:00:00',
        end: this.formatDate(this.currentDate) + ' 23:59:59'
      };
    } else {
      const firstDay = new Date(year, month, 1);
      const lastDay = new Date(year, month + 1, 14);
      return {
        start: this.formatDate(firstDay) + ' 00:00:00',
        end: this.formatDate(lastDay) + ' 23:59:59'
      };
    }
  },

  renderLoading() {
    const titleEl = document.getElementById('calendar-title');
    if (titleEl) {
      this.updateTitle();
    }
  },

  render() {
    this.updateTitle();
    const container = document.getElementById('calendar-render-area');
    if (!container) return;

    if (this.currentView === 'month') {
      container.innerHTML = this.renderMonthView();
    } else if (this.currentView === 'week') {
      container.innerHTML = this.renderWeekView();
    } else if (this.currentView === 'day') {
      container.innerHTML = this.renderDayView();
    } else if (this.currentView === 'agenda') {
      container.innerHTML = this.renderAgendaView();
    }

    this.attachCalendarInteractions();
  },

  updateTitle() {
    const titleEl = document.getElementById('calendar-title');
    if (!titleEl) return;

    const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    const curYear = this.currentDate.getFullYear();
    const curMonth = months[this.currentDate.getMonth()];

    if (this.currentView === 'month') {
      titleEl.textContent = `${curMonth} ${curYear}`;
    } else if (this.currentView === 'week') {
      const start = new Date(this.currentDate);
      const dayOffset = (start.getDay() + 6) % 7;
      start.setDate(start.getDate() - dayOffset);
      const end = new Date(start);
      end.setDate(end.getDate() + 6);

      const startMonth = months[start.getMonth()].substring(0, 3);
      const endMonth = months[end.getMonth()].substring(0, 3);

      if (start.getMonth() === end.getMonth()) {
        titleEl.textContent = `${startMonth} ${start.getDate()} – ${end.getDate()}, ${curYear}`;
      } else {
        titleEl.textContent = `${startMonth} ${start.getDate()} – ${endMonth} ${end.getDate()}, ${curYear}`;
      }
    } else if (this.currentView === 'day') {
      const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
      const dayName = days[this.currentDate.getDay()];
      titleEl.textContent = `${dayName}, ${curMonth} ${this.currentDate.getDate()}, ${curYear}`;
    } else if (this.currentView === 'agenda') {
      titleEl.textContent = `Agenda • ${curMonth} ${curYear}`;
    }
  },

  renderMonthView() {
    const year = this.currentDate.getFullYear();
    const month = this.currentDate.getMonth();
    const todayStr = this.formatDate(new Date());

    const firstDay = new Date(year, month, 1);
    const startDayIndex = (firstDay.getDay() + 6) % 7;
    const startDate = new Date(year, month, 1 - startDayIndex);

    let html = '<div class="calendar-grid">';
    
    // Day headers
    const dayNames = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
    dayNames.forEach(d => {
      html += `<div class="calendar-day-head">${d}</div>`;
    });

    // 42 compact fixed cells
    for (let i = 0; i < 42; i++) {
      const cellDate = new Date(startDate);
      cellDate.setDate(startDate.getDate() + i);
      const dateStr = this.formatDate(cellDate);
      const isCurrentMonth = cellDate.getMonth() === month;
      const isToday = dateStr === todayStr;

      // Filter events for this date
      const dayEvents = this.events.filter(e => (e.start || '').substring(0, 10) === dateStr);
      const hasEvents = dayEvents.length > 0;

      html += `
        <div class="calendar-cell ${!isCurrentMonth ? 'other-month' : ''} ${isToday ? 'today' : ''}" data-date="${dateStr}">
          <div class="cell-top-bar">
            <div class="cell-date-num">${cellDate.getDate()}</div>
            ${hasEvents ? `<span class="cell-count-pill">${dayEvents.length}</span>` : ''}
          </div>
          
          <div class="cell-icons-row">
            ${dayEvents.slice(0, 4).map(e => `
              <span class="event-icon-badge" style="background-color:${e.color || '#4F46E5'}20;color:${e.color || '#4F46E5'};border:1px solid ${e.color || '#4F46E5'}45;" data-event-id="${e.id}">
                <i class="${e.category_icon || 'fa-solid fa-calendar'}"></i>
              </span>
            `).join('')}
            ${dayEvents.length > 4 ? `<span style="font-size:9px;font-weight:700;color:var(--text-subtle);margin-left:1px;">+${dayEvents.length - 4}</span>` : ''}
          </div>
        </div>
      `;
    }

    html += '</div>';
    return html;
  },

  renderWeekView() {
    const todayStr = this.formatDate(new Date());
    const start = new Date(this.currentDate);
    const dayOffset = (start.getDay() + 6) % 7;
    start.setDate(start.getDate() - dayOffset);

    const weekDays = [];
    for (let i = 0; i < 7; i++) {
      const d = new Date(start);
      d.setDate(start.getDate() + i);
      weekDays.push(d);
    }

    const now = new Date();
    const currentHour = now.getHours();
    const currentMin = now.getMinutes();
    const currentLineTop = (currentHour * 48) + (currentMin / 60 * 48) + 40;

    let html = `
      <div class="time-grid-container" style="--day-cols: 7;">
        <div class="time-grid-hours">
          <div class="time-grid-header-corner"></div>
          ${Array.from({length: 24}).map((_, h) => `
            <div class="time-hour-slot">${String(h).padStart(2, '0')}:00</div>
          `).join('')}
        </div>
        <div class="time-grid-days">
    `;

    weekDays.forEach(d => {
      const dateStr = this.formatDate(d);
      const isToday = dateStr === todayStr;
      const dayEvents = this.events.filter(e => (e.start || '').substring(0, 10) === dateStr);

      html += `
        <div class="time-day-col ${isToday ? 'is-today-col' : ''}" data-date="${dateStr}">
          <div class="time-day-col-header ${isToday ? 'today-head' : ''}">
            <div style="font-size:11px;text-transform:uppercase;color:var(--text-subtle);font-weight:600;">
              ${d.toLocaleDateString('en-US', { weekday: 'short' })}
            </div>
            <div class="col-date-badge ${isToday ? 'active-badge' : ''}">
              ${d.getDate()}
            </div>
          </div>
          ${isToday ? `<div class="current-time-line" style="top:${currentLineTop}px;"></div>` : ''}
          ${Array.from({length: 24}).map((_, h) => `
            <div class="time-slot-line" data-hour="${h}" data-date="${dateStr}"></div>
          `).join('')}
          ${dayEvents.map(e => {
            const timeInfo = this.parseEventTime(e);
            return `
              <div class="time-event-block" style="top:${timeInfo.top}px;height:${timeInfo.height}px;background-color:${e.color || '#4F46E5'};display:flex;align-items:center;justify-content:center;border-radius:4px;" data-event-id="${e.id}">
                <i class="${e.category_icon || 'fa-solid fa-calendar'}" style="color:#FFF;font-size:12px;"></i>
              </div>
            `;
          }).join('')}
        </div>
      `;
    });

    html += `
        </div>
      </div>
    `;
    return html;
  },

  renderDayView() {
    const dateStr = this.formatDate(this.currentDate);
    const todayStr = this.formatDate(new Date());
    const isToday = dateStr === todayStr;
    const dayEvents = this.events.filter(e => (e.start || '').substring(0, 10) === dateStr);

    const now = new Date();
    const currentHour = now.getHours();
    const currentMin = now.getMinutes();
    const currentLineTop = (currentHour * 48) + (currentMin / 60 * 48) + 40;

    let html = `
      <div class="time-grid-container" style="--day-cols: 1;">
        <div class="time-grid-hours">
          <div class="time-grid-header-corner"></div>
          ${Array.from({length: 24}).map((_, h) => `
            <div class="time-hour-slot">${String(h).padStart(2, '0')}:00</div>
          `).join('')}
        </div>
        <div class="time-grid-days">
          <div class="time-day-col ${isToday ? 'is-today-col' : ''}" data-date="${dateStr}">
            <div class="time-day-col-header ${isToday ? 'today-head' : ''}">
              <div style="font-size:13px;font-weight:700;color:var(--text-main);">
                ${this.currentDate.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' })}
              </div>
            </div>
            ${isToday ? `<div class="current-time-line" style="top:${currentLineTop}px;"></div>` : ''}
            ${Array.from({length: 24}).map((_, h) => `
              <div class="time-slot-line" data-hour="${h}" data-date="${dateStr}"></div>
            `).join('')}
            ${dayEvents.map(e => {
              const timeInfo = this.parseEventTime(e);
              return `
                <div class="time-event-block" style="top:${timeInfo.top}px;height:${timeInfo.height}px;background-color:${e.color || '#4F46E5'};display:flex;align-items:center;justify-content:center;border-radius:4px;" data-event-id="${e.id}">
                  <i class="${e.category_icon || 'fa-solid fa-calendar'}" style="color:#FFF;font-size:14px;"></i>
                </div>
              `;
            }).join('')}
          </div>
        </div>
      </div>
    `;
    return html;
  },

  renderAgendaView() {
    if (this.events.length === 0) {
      return `
        <div style="text-align:center;padding:56px 20px;">
          <div style="width:52px;height:52px;border-radius:50%;background:var(--bg-subtle);display:flex;align-items:center;justify-content:center;margin:0 auto 12px;color:var(--text-subtle);font-size:22px;">
            <i class="fa-regular fa-calendar-xmark"></i>
          </div>
          <h3 style="font-weight:700;font-size:15px;margin-bottom:4px;color:var(--text-main);">No Events Found</h3>
          <p style="color:var(--text-muted);font-size:12.5px;max-width:320px;margin:0 auto 16px;">There are no scheduled events matching your current range and filters.</p>
          <a href="/events/create" class="btn btn-primary" style="display:inline-flex;align-items:center;gap:6px;font-size:13px;padding:6px 14px;text-decoration:none;">
            <i class="fa-solid fa-plus"></i> Create New Event
          </a>
        </div>
      `;
    }

    // Group events by date
    const groups = {};
    this.events.forEach(e => {
      const dateKey = (e.start || '').substring(0, 10);
      if (!dateKey) return;
      if (!groups[dateKey]) groups[dateKey] = [];
      groups[dateKey].push(e);
    });

    let html = '<div style="display:flex;flex-direction:column;">';

    Object.keys(groups).sort().forEach(dateKey => {
      const dateObj = new Date(dateKey + 'T00:00:00');
      const dateFormatted = dateObj.toLocaleDateString('en-US', { weekday: 'long', month: 'short', day: 'numeric', year: 'numeric' });
      const isToday = dateKey === this.formatDate(new Date());

      html += `
        <div style="border-bottom:1px solid var(--border-color);">
          <div style="background-color:var(--bg-subtle);padding:10px 18px;display:flex;align-items:center;justify-content:space-between;">
            <span style="font-weight:700;font-size:12.5px;color:var(--text-main);display:flex;align-items:center;gap:8px;">
              <i class="fa-regular fa-calendar" style="color:var(--color-primary);"></i>
              ${dateFormatted}
              ${isToday ? '<span class="badge badge-primary" style="font-size:9.5px;padding:1px 5px;">Today</span>' : ''}
            </span>
            <span class="badge" style="background:var(--bg-surface);color:var(--text-muted);border:1px solid var(--border-color);font-size:10px;">
              ${groups[dateKey].length} ${groups[dateKey].length === 1 ? 'event' : 'events'}
            </span>
          </div>
          <table style="width:100%;border-collapse:collapse;">
            <tbody>
              ${groups[dateKey].map(e => {
                const startTime = (e.start || '').length >= 16 ? e.start.substring(11, 16) : '';
                const endTime = (e.end || '').length >= 16 ? e.end.substring(11, 16) : '';
                const timeRange = startTime && endTime ? `${startTime} – ${endTime}` : startTime;

                return `
                  <tr class="agenda-table-row" onclick="window.location.href='/events/${e.id}'" style="border-bottom:1px solid var(--border-color);cursor:pointer;">
                    <td style="padding:11px 18px;width:120px;font-weight:600;font-size:12px;color:var(--text-muted);white-space:nowrap;">
                      <i class="fa-regular fa-clock" style="margin-right:4px;font-size:11px;"></i>
                      ${timeRange || 'All Day'}
                    </td>
                    <td style="padding:11px 18px;">
                      <div style="font-weight:600;font-size:13px;color:var(--text-main);display:flex;align-items:center;gap:8px;">
                        <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background-color:${e.color || '#4F46E5'};flex-shrink:0;"></span>
                        <span>${this.escapeHtml(e.title)}</span>
                      </div>
                      ${e.location ? `<div style="font-size:11.5px;color:var(--text-muted);margin-top:2px;display:flex;align-items:center;gap:4px;"><i class="fa-solid fa-location-dot" style="font-size:10px;"></i>${this.escapeHtml(e.location)}</div>` : ''}
                    </td>
                    <td style="padding:11px 18px;text-align:right;white-space:nowrap;">
                      <span class="badge" style="background-color:${e.color || '#4F46E5'}18;color:${e.color || '#4F46E5'};border:1px solid ${e.color || '#4F46E5'}40;font-size:10.5px;">
                        ${this.escapeHtml(e.category_name || 'General')}
                      </span>
                    </td>
                  </tr>
                `;
              }).join('')}
            </tbody>
          </table>
        </div>
      `;
    });

    html += '</div>';
    return html;
  },

  parseEventTime(e) {
    let startH = 9, startM = 0, endH = 10, endM = 0;
    if (e.start && e.start.length >= 16) {
      startH = parseInt(e.start.substring(11, 13), 10) || 0;
      startM = parseInt(e.start.substring(14, 16), 10) || 0;
    }
    if (e.end && e.end.length >= 16) {
      endH = parseInt(e.end.substring(11, 13), 10) || 0;
      endM = parseInt(e.end.substring(14, 16), 10) || 0;
    }

    const top = (startH * 48) + (startM / 60 * 48) + 40;
    let durHours = (endH - startH) + ((endM - startM) / 60);
    if (durHours <= 0) durHours = 1;
    const height = Math.max(24, durHours * 48);

    const sStr = `${String(startH).padStart(2, '0')}:${String(startM).padStart(2, '0')}`;
    const eStr = `${String(endH).padStart(2, '0')}:${String(endM).padStart(2, '0')}`;

    return {
      top,
      height,
      label: `${sStr} – ${eStr}`
    };
  },

  attachCalendarInteractions() {
    // Month Cell Hover & Click Popover
    document.querySelectorAll('.calendar-cell').forEach(cell => {
      // Right-click on cell -> Pin details popover (stays open until closed/clicked outside)
      cell.addEventListener('contextmenu', (e) => {
        e.preventDefault();
        e.stopPropagation();
        const dateStr = cell.dataset.date;
        if (!dateStr) return;
        this.showPopover(cell, dateStr, true);
      });

      // Click on cell
      cell.addEventListener('click', (e) => {
        e.stopPropagation();
        if (this.isPinned) {
          this.hidePopover(true);
        } else {
          const dateStr = cell.dataset.date;
          if (dateStr) {
            this.showPopover(cell, dateStr, false);
          }
        }
      });

      // Hover over cell to preview details if events exist (unpinned)
      cell.addEventListener('mouseenter', (e) => {
        if (this.isPinned) return;
        this.cancelHidePopover();
        const dateStr = cell.dataset.date;
        if (!dateStr) return;
        const dayEvents = this.events.filter(ev => (ev.start || '').substring(0, 10) === dateStr);
        if (dayEvents.length > 0) {
          this.showPopover(cell, dateStr, false);
        } else {
          this.hidePopover(false);
        }
      });

      // Mouse leaves cell -> automatically schedule hide if not pinned
      cell.addEventListener('mouseleave', (e) => {
        if (!this.isPinned) {
          this.scheduleHidePopover(150);
        }
      });

      // Double click to open new event
      cell.addEventListener('dblclick', (e) => {
        const dateStr = cell.dataset.date;
        if (dateStr) {
          this.openCreateModalForDate(dateStr, 9);
        }
      });
    });

    // Time slot double click & right click (Week / Day view)
    document.querySelectorAll('.time-slot-line').forEach(slot => {
      slot.addEventListener('contextmenu', (e) => {
        e.preventDefault();
        e.stopPropagation();
        const dateStr = slot.dataset.date;
        if (dateStr) {
          this.showPopover(slot, dateStr, true);
        }
      });

      slot.addEventListener('dblclick', (e) => {
        const dateStr = slot.dataset.date;
        const hour = slot.dataset.hour ? parseInt(slot.dataset.hour, 10) : 9;
        if (dateStr) {
          this.openCreateModalForDate(dateStr, hour);
        }
      });
    });

    // Time event block click & right-click
    document.querySelectorAll('.time-event-block').forEach(block => {
      block.addEventListener('contextmenu', (e) => {
        e.preventDefault();
        e.stopPropagation();
        const parent = block.closest('.time-day-col');
        const dateStr = parent ? parent.dataset.date : null;
        if (dateStr) {
          this.showPopover(block, dateStr, true);
        }
      });

      block.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        const eventId = block.dataset.eventId;
        if (eventId) {
          window.location.href = `/events/${eventId}`;
        }
      });
    });
  },

  showPopover(cell, dateStr, isPinned = false) {
    this.cancelHidePopover();
    if (!this.popoverEl) return;
    this.isPinned = isPinned;

    // Highlight active cell
    document.querySelectorAll('.calendar-cell.selected').forEach(c => c.classList.remove('selected'));
    cell.classList.add('selected');

    const dayEvents = this.events.filter(e => (e.start || '').substring(0, 10) === dateStr);
    const dateObj = new Date(dateStr + 'T00:00:00');
    const formattedDate = dateObj.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric' });

    let html = `
      <div class="popover-header">
        <div class="popover-title">
          <i class="fa-regular fa-calendar" style="color:var(--color-primary);"></i>
          <span>${formattedDate}</span>
          ${this.isPinned ? '<span class="badge" style="font-size:9.5px;padding:1px 5px;background:var(--color-primary-light);color:var(--color-primary);">Details</span>' : ''}
        </div>
        ${this.isPinned ? `
          <button class="popover-close" onclick="DischeduleCalendar.hidePopover(true)" title="Close">
            <i class="fa-solid fa-xmark"></i>
          </button>
        ` : ''}
      </div>

      <div class="popover-body">
    `;

    if (dayEvents.length === 0) {
      html += `
        <div style="padding:18px 14px;text-align:center;color:var(--text-muted);font-size:12px;">
          <i class="fa-regular fa-calendar-check" style="font-size:20px;margin-bottom:6px;display:block;opacity:0.6;"></i>
          No events scheduled on this day.
        </div>
      `;
    } else {
      dayEvents.forEach(e => {
        const startTime = (e.start || '').length >= 16 ? e.start.substring(11, 16) : '';
        const endTime = (e.end || '').length >= 16 ? e.end.substring(11, 16) : '';
        const timeStr = startTime && endTime ? `${startTime} - ${endTime}` : startTime;

        html += `
          <div class="popover-event-item" onclick="window.location.href='/events/${e.id}'">
            <div class="popover-event-header">
              <div class="popover-event-title">
                <span style="width:8px;height:8px;border-radius:50%;background-color:${e.color || '#4F46E5'};flex-shrink:0;"></span>
                <span>${this.escapeHtml(e.title)}</span>
              </div>
              <span class="badge" style="font-size:9.5px;padding:2px 5px;background-color:${e.color || '#4F46E5'}18;color:${e.color || '#4F46E5'};">
                ${this.escapeHtml(e.category_name || 'General')}
              </span>
            </div>
            
            <div class="popover-event-meta">
              <span><i class="fa-regular fa-clock" style="font-size:10px;"></i> ${timeStr || 'All Day'}</span>
              ${e.location ? `<span><i class="fa-solid fa-location-dot" style="font-size:10px;"></i> ${this.escapeHtml(e.location)}</span>` : ''}
              ${e.priority === 'high' ? '<span class="badge badge-danger" style="font-size:9px;">Urgent</span>' : ''}
            </div>
          </div>
        `;
      });
    }

    html += `</div>`;

    if (this.isPinned) {
      html += `
        <div class="popover-footer">
          <span style="font-size:11px;color:var(--text-muted);font-weight:600;">
            ${dayEvents.length} ${dayEvents.length === 1 ? 'event' : 'events'}
          </span>
          <a href="/events/create?date=${encodeURIComponent(dateStr)}" class="btn btn-primary" style="padding:4px 12px;font-size:11.5px;border-radius:var(--radius-sm);text-decoration:none;display:inline-flex;align-items:center;gap:4px;">
            <i class="fa-solid fa-plus"></i> Add Event
          </a>
        </div>
      `;
    } else {
      html += `
        <div class="popover-footer" style="padding:6px 14px;background-color:var(--bg-subtle);">
          <span style="font-size:10.5px;color:var(--text-muted);font-weight:500;">
            ${dayEvents.length} ${dayEvents.length === 1 ? 'event' : 'events'}
          </span>
        </div>
      `;
    }

    this.popoverEl.innerHTML = html;
    this.popoverEl.classList.add('active');

    // Position popover relative to cell with boundary detection
    const rect = cell.getBoundingClientRect();
    const popoverWidth = 320;
    let left = rect.left + (rect.width / 2) - (popoverWidth / 2);
    let top = rect.bottom + 6;

    if (left < 12) left = 12;
    if (left + popoverWidth > window.innerWidth - 12) {
      left = window.innerWidth - popoverWidth - 12;
    }

    if (top + 280 > window.innerHeight) {
      top = rect.top - 280;
    }
    if (top < 12) top = 12;

    this.popoverEl.style.left = `${left}px`;
    this.popoverEl.style.top = `${top}px`;
  },

  hidePopover(force = false) {
    if (!force && this.isPinned) return;
    this.cancelHidePopover();
    this.isPinned = false;
    if (this.popoverEl) {
      this.popoverEl.classList.remove('active');
    }
    document.querySelectorAll('.calendar-cell.selected').forEach(c => c.classList.remove('selected'));
  },

  openCreateModalForDate(dateStr, startHour = 9) {
    this.hidePopover();
    const endHour = (startHour + 1) % 24;
    const startVal = `${dateStr}T${String(startHour).padStart(2, '0')}:00`;
    const endVal = `${dateStr}T${String(endHour).padStart(2, '0')}:00`;

    window.location.href = `/events/create?start=${encodeURIComponent(startVal)}&end=${encodeURIComponent(endVal)}`;
  },

  formatDate(d) {
    const year = d.getFullYear();
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
  },

  escapeHtml(str) {
    if (!str) return '';
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }
};
