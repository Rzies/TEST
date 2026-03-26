(function () {
  const root = document.body;
  const sidebar = document.getElementById('sidebar');
  const toggleBtn = document.getElementById('toggleSidebar');
  const closeBtn = document.getElementById('closeSidebar');

  if (localStorage.getItem('sidebarCollapsed') === '1' && window.innerWidth >= 992) {
    root.classList.add('sidebar-collapsed');
  }

  toggleBtn?.addEventListener('click', () => {
    if (window.innerWidth < 992) {
      root.classList.toggle('sidebar-open');
      return;
    }
    root.classList.toggle('sidebar-collapsed');
    localStorage.setItem('sidebarCollapsed', root.classList.contains('sidebar-collapsed') ? '1' : '0');
  });

  closeBtn?.addEventListener('click', () => root.classList.remove('sidebar-open'));

  document.addEventListener('click', (e) => {
    if (window.innerWidth < 992 && root.classList.contains('sidebar-open') && sidebar && !sidebar.contains(e.target) && e.target !== toggleBtn) {
      root.classList.remove('sidebar-open');
    }
  });

  if (window.dashboardData && document.getElementById('onuStatusChart')) {
    const chart = echarts.init(document.getElementById('onuStatusChart'));
    chart.setOption({
      tooltip: { trigger: 'item' },
      legend: { bottom: 0 },
      series: [{
        type: 'pie',
        radius: ['48%', '72%'],
        avoidLabelOverlap: false,
        itemStyle: { borderRadius: 8, borderColor: '#fff', borderWidth: 3 },
        label: { formatter: '{b}: {d}%' },
        data: [
          { value: window.dashboardData.online, name: 'Online', itemStyle: { color: '#22c55e' } },
          { value: window.dashboardData.offline, name: 'Offline', itemStyle: { color: '#ef4444' } }
        ]
      }]
    });
    window.addEventListener('resize', () => chart.resize());
  }

  if (window.mapData && document.getElementById('networkMap')) {
    const map = L.map('networkMap').setView([parseFloat(window.mapData.server.latitude), parseFloat(window.mapData.server.longitude)], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    const meterDistance = (aLat, aLon, bLat, bLon) => {
      const R = 6371000;
      const toRad = (v) => v * (Math.PI / 180);
      const dLat = toRad(bLat - aLat);
      const dLon = toRad(bLon - aLon);
      const x = Math.sin(dLat / 2) ** 2 + Math.cos(toRad(aLat)) * Math.cos(toRad(bLat)) * Math.sin(dLon / 2) ** 2;
      return R * 2 * Math.atan2(Math.sqrt(x), Math.sqrt(1 - x));
    };

    L.marker([window.mapData.server.latitude, window.mapData.server.longitude])
      .addTo(map)
      .bindPopup(`<b>Server:</b> ${window.mapData.server.name}<br>${window.mapData.server.description}`);

    window.mapData.odps.forEach((odp) => {
      L.circleMarker([odp.latitude, odp.longitude], { radius: 8, color: '#1d4ed8' })
        .addTo(map)
        .bindPopup(`<b>ODP:</b> ${odp.name}<br>Total client: ${odp.total_clients}`);
    });

    window.mapData.clients.forEach((client) => {
      const d = meterDistance(+client.odp_lat, +client.odp_lon, +client.latitude, +client.longitude);
      const marker = L.marker([client.latitude, client.longitude]);
      marker.addTo(map).bindPopup(
        `<b>${client.name}</b><br>Status: ${client.status}<br>ODP: ${client.odp_name}<br>Jarak: ${Math.round(d)} meter`
      );
      L.polyline([[client.odp_lat, client.odp_lon], [client.latitude, client.longitude]], { color: '#94a3b8', weight: 1.4, dashArray: '4,4' }).addTo(map);
    });
  }

  const table = document.getElementById('devicesTable');
  const search = document.getElementById('deviceSearch');
  const statusFilter = document.getElementById('deviceStatusFilter');

  function filterDevices() {
    if (!table) return;
    const q = (search?.value || '').toLowerCase();
    const status = statusFilter?.value || 'all';

    table.querySelectorAll('tbody tr').forEach((row) => {
      const text = row.textContent.toLowerCase();
      const rowStatus = row.getAttribute('data-status');
      const ok = text.includes(q) && (status === 'all' || status === rowStatus);
      row.style.display = ok ? '' : 'none';
    });
  }

  search?.addEventListener('input', filterDevices);
  statusFilter?.addEventListener('change', filterDevices);

  const modalEl = document.getElementById('deviceModal');
  if (modalEl) {
    const modal = new bootstrap.Modal(modalEl);
    document.querySelectorAll('.view-device').forEach((btn) => {
      btn.addEventListener('click', () => {
        document.getElementById('deviceModalTitle').textContent = `Detail Device - ${btn.dataset.name}`;
        let formatted = btn.dataset.json;
        try {
          formatted = JSON.stringify(JSON.parse(btn.dataset.json), null, 2);
        } catch (e) {
          // noop
        }
        document.getElementById('deviceJson').textContent = formatted;
        modal.show();
      });
    });
  }
})();
