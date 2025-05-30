<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>健康检查面板</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: "Segoe UI", Arial, sans-serif; margin: 20px; }
        h1 { color: #2c3e50; }
        .status-up { color: #27ae60; }
        .status-down { color: #e74c3c; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background: #f4f4f4; }
        .filter { margin-bottom: 10px; }
        .export-btn { margin-left: 10px; }
    </style>
</head>
<body>
    <h1>健康检查面板</h1>
    <section>
        <h2>服务健康状态</h2>
        <table id="status-table">
            <thead>
                <tr>
                    <th>服务</th>
                    <th>状态</th>
                    <th>耗时(ms)</th>
                    <th>信息</th>
                    <th>时间</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
        <button onclick="refreshStatus()">刷新</button>
    </section>
    <section>
        <h2>调用日志</h2>
        <div class="filter">
            服务: <input type="text" id="log-service" size="10">
            状态: <select id="log-status">
                <option value="">全部</option>
                <option value="UP">UP</option>
                <option value="DOWN">DOWN</option>
            </select>
            <button onclick="loadLogs()">筛选</button>
            <button class="export-btn" onclick="exportCSV()">导出 CSV</button>
        </div>
        <table id="logs-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>服务</th>
                    <th>状态</th>
                    <th>耗时(ms)</th>
                    <th>信息</th>
                    <th>用户</th>
                    <th>IP</th>
                    <th>时间</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
        <div id="logs-pagination"></div>
    </section>
    <script>
        function refreshStatus() {
            fetch('/health/status')
                .then(res => res.json())
                .then(data => {
                    const tbody = document.querySelector('#status-table tbody');
                    tbody.innerHTML = '';
                    (data.data || []).forEach(row => {
                        tbody.innerHTML += `<tr>
                            <td>${row.service}</td>
                            <td class="${row.status === 'UP' ? 'status-up' : 'status-down'}">${row.status}</td>
                            <td>${row.latency}</td>
                            <td>${row.message}</td>
                            <td>${row.time}</td>
                        </tr>`;
                    });
                });
        }
        function loadLogs(page = 1) {
            const service = document.getElementById('log-service').value;
            const status = document.getElementById('log-status').value;
            let url = `/health/logs?per_page=20&page=${page}`;
            if (service) url += `&service=${encodeURIComponent(service)}`;
            if (status) url += `&status=${encodeURIComponent(status)}`;
            fetch(url)
                .then(res => res.json())
                .then(data => {
                    const tbody = document.querySelector('#logs-table tbody');
                    tbody.innerHTML = '';
                    (data.data || []).forEach(row => {
                        tbody.innerHTML += `<tr>
                            <td>${row.id}</td>
                            <td>${row.service}</td>
                            <td class="${row.status === 'UP' ? 'status-up' : 'status-down'}">${row.status}</td>
                            <td>${row.latency}</td>
                            <td>${row.message}</td>
                            <td>${row.user || ''}</td>
                            <td>${row.ip || ''}</td>
                            <td>${row.checked_at}</td>
                        </tr>`;
                    });
                    // 分页
                    let html = '';
                    if (data.last_page > 1) {
                        for (let i = 1; i <= data.last_page; i++) {
                            html += `<button onclick="loadLogs(${i})"${i === data.current_page ? ' style="font-weight:bold"' : ''}>${i}</button> `;
                        }
                    }
                    document.getElementById('logs-pagination').innerHTML = html;
                });
        }
        function exportCSV() {
            const service = document.getElementById('log-service').value;
            const status = document.getElementById('log-status').value;
            let url = `/health/logs?per_page=1000`;
            if (service) url += `&service=${encodeURIComponent(service)}`;
            if (status) url += `&status=${encodeURIComponent(status)}`;
            fetch(url)
                .then(res => res.json())
                .then(data => {
                    let csv = 'ID,服务,状态,耗时(ms),信息,用户,IP,时间\n';
                    (data.data || []).forEach(row => {
                        csv += [
                            row.id, row.service, row.status, row.latency, `"${row.message}"`,
                            row.user || '', row.ip || '', row.checked_at
                        ].join(',') + '\n';
                    });
                    const blob = new Blob([csv], {type: 'text/csv'});
                    const link = document.createElement('a');
                    link.href = URL.createObjectURL(blob);
                    link.download = 'health_logs.csv';
                    link.click();
                });
        }
        // 初始化
        refreshStatus();
        loadLogs();
    </script>
</body>
</html>
