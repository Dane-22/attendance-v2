<?php
$title = 'Site Attendance';
ob_start();
?>

<style>
    .welcome-banner {
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 20px 24px;
        margin-bottom: 24px;
    }

    .welcome-banner h1 {
        color: var(--accent-color);
        font-size: 1.25rem;
        font-weight: 600;
        margin: 0;
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
    }

    .section-title {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--accent-color);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .btn-primary {
        background: var(--accent-color);
        color: #000000;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: opacity 0.2s;
    }

    .btn-primary:hover {
        opacity: 0.9;
    }

    .btn-secondary {
        background: var(--bg-secondary);
        color: var(--text-primary);
        border: 1px solid var(--border-color);
        padding: 8px 16px;
        border-radius: 6px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-secondary:hover {
        border-color: var(--accent-color);
    }

    .btn-success {
        background: #10b981;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        font-weight: 500;
        cursor: pointer;
    }

    .btn-danger {
        background: #ef4444;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        font-weight: 500;
        cursor: pointer;
    }

    .search-box {
        background: var(--bg-primary);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 12px 16px;
        color: var(--text-primary);
        width: 300px;
        font-size: 0.875rem;
    }

    .search-box::placeholder {
        color: var(--text-secondary);
    }

    .projects-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .project-card {
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 20px;
        position: relative;
        transition: border-color 0.2s;
        cursor: pointer;
    }

    .project-card:hover {
        border-color: var(--accent-color);
    }

    .project-card.selected {
        border-color: var(--accent-color);
        background: linear-gradient(135deg, rgba(255, 215, 0, 0.05), var(--bg-secondary));
    }

    .project-card.loading {
        opacity: 0.7;
        pointer-events: none;
    }

    .project-name {
        font-weight: 600;
        color: var(--accent-color);
        font-size: 0.95rem;
        margin-bottom: 8px;
    }

    .project-desc {
        color: var(--text-secondary);
        font-size: 0.8rem;
        line-height: 1.4;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }

    .stat-card {
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 20px;
        text-align: center;
    }

    .stat-label {
        color: var(--text-secondary);
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 12px;
    }

    .stat-value {
        color: var(--accent-color);
        font-size: 2rem;
        font-weight: 700;
        font-family: monospace;
    }

    .filter-pills {
        display: flex;
        gap: 8px;
        margin-bottom: 20px;
    }

    .filter-pill {
        padding: 8px 16px;
        border-radius: 20px;
        border: none;
        font-size: 0.75rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .filter-pill.active {
        background: var(--accent-color);
        color: #000000;
    }

    .filter-pill:not(.active) {
        background: var(--bg-secondary);
        color: var(--text-secondary);
        border: 1px solid var(--border-color);
    }

    .filter-pill:not(.active):hover {
        color: var(--text-primary);
    }

    .employee-search-row {
        display: flex;
        gap: 12px;
        margin-bottom: 20px;
    }

    .employee-search-row .search-box {
        flex: 1;
    }

    .btn-undo {
        background: var(--bg-secondary);
        color: var(--text-secondary);
        border: 1px solid var(--border-color);
        padding: 10px 20px;
        border-radius: 8px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .employee-placeholder {
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 60px 20px;
        text-align: center;
        color: var(--text-secondary);
    }

    .employee-placeholder i {
        font-size: 3rem;
        margin-bottom: 16px;
        opacity: 0.5;
    }

    .employee-table-container {
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        overflow: hidden;
    }

    .employee-table {
        width: 100%;
        border-collapse: collapse;
    }

    .employee-table th {
        padding: 8px 12px;
        text-align: left;
        font-size: 0.7rem;
        font-weight: 600;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid var(--border-color);
        white-space: nowrap;
    }

    .employee-table th:first-child {
        width: 40px;
        text-align: center;
    }

    .employee-table th:nth-child(2) {
        width: auto;
    }

    .employee-table th:nth-child(3),
    .employee-table th:nth-child(4) {
        width: 100px;
        text-align: center;
    }

    .employee-table th:nth-child(5) {
        width: 100px;
        text-align: center;
    }

    .employee-table th.actions-col {
        width: 220px;
        text-align: right;
    }

    .employee-table td {
        padding: 8px 12px;
        border-bottom: 1px solid var(--border-color);
        vertical-align: middle;
    }

    .employee-table tr:last-child td {
        border-bottom: none;
    }

    .employee-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .employee-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea, #764ba2);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-weight: 600;
        font-size: 0.8rem;
    }

    .employee-details h4 {
        color: var(--text-primary);
        font-size: 0.9rem;
        font-weight: 600;
        margin-bottom: 2px;
    }

    .employee-details p {
        color: var(--text-secondary);
        font-size: 0.75rem;
    }

    .employee-details .project-tag {
        color: #fbbf24;
        font-size: 0.7rem;
    }

    .time-cell {
        color: var(--text-secondary);
        font-size: 0.85rem;
    }

    .hours-cell {
        color: var(--text-secondary);
        font-size: 0.85rem;
    }

    .actions-cell {
        display: flex;
        gap: 8px;
        justify-content: flex-end;
    }

    .btn-pill {
        padding: 6px 14px;
        border-radius: 20px;
        border: none;
        font-size: 0.7rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        white-space: nowrap;
    }

    .btn-absent {
        background: #ef4444;
        color: white;
    }

    .btn-absent:hover {
        background: #dc2626;
    }

    .btn-timein {
        background: #10b981;
        color: white;
    }

    .btn-timein:hover {
        background: #059669;
    }

    .btn-checkout {
        background: #6b7280;
        color: white;
    }

    .btn-checkout:hover {
        background: #4b5563;
    }

    .pagination-row {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 8px;
        margin-top: 16px;
    }

    .pagination-btn {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        border: 1px solid var(--border-color);
        background: var(--bg-secondary);
        color: var(--text-secondary);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.875rem;
    }

    .pagination-btn.active {
        background: var(--accent-color);
        color: #000000;
        border-color: var(--accent-color);
    }

    .pagination-btn.next {
        width: auto;
        padding: 0 12px;
    }

    .employee-actions {
        display: flex;
        gap: 8px;
        justify-content: flex-end;
    }

    .quick-tips {
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 20px 24px;
        margin-top: 24px;
    }

    .quick-tips h3 {
        color: var(--accent-color);
        font-size: 1rem;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .tips-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 20px;
    }

    .tip-item {
        font-size: 0.8rem;
        color: var(--text-secondary);
        line-height: 1.5;
    }

    .tip-item strong {
        color: var(--accent-color);
    }

    .loading-spinner {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 2px solid var(--border-color);
        border-top-color: var(--accent-color);
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    @media (max-width: 1024px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        .tips-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }
        .tips-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        .search-box {
            width: 100%;
        }
        .section-header {
            flex-direction: column;
            gap: 12px;
            align-items: stretch;
        }
        .employee-item {
            grid-template-columns: 50px 1fr;
            gap: 12px;
        }
        .employee-status, .employee-actions {
            grid-column: 2;
            justify-content: flex-start;
        }
    }
</style>

<!-- Welcome Banner -->
<div class="welcome-banner">
    <h1>Welcome! Please select a project to start!</h1>
</div>

<!-- Projects Section -->
<div class="section-header">
    <span class="section-title">SELECT DEPLOYMENT PROJECT</span>
    <a href="<?= dirname($_SERVER['SCRIPT_NAME']) ?>/branches/create" class="btn-primary">
        <i class="fas fa-plus"></i> Add Project
    </a>
</div>

<div class="search-box" style="margin-bottom: 16px; width: 100%;">
    <input type="text" id="projectSearch" placeholder="Search projects..." style="background: transparent; border: none; color: var(--text-primary); width: 100%; outline: none;">
</div>

<div class="projects-grid" id="projectsGrid">
    <?php foreach ($branches as $index => $branch): ?>
    <div class="project-card <?= $index === 0 ? 'selected' : '' ?>" 
         data-branch-code="<?= htmlspecialchars($branch['branch_code']) ?>"
         onclick="selectProject(this, '<?= htmlspecialchars($branch['branch_code']) ?>')">
        <div class="project-name"><?= htmlspecialchars($branch['branch_name']) ?></div>
        <div class="project-desc">Branch Code: <?= htmlspecialchars($branch['branch_code']) ?> - Deploy employees to this project for attendance.</div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Stats Section -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">TOTAL WORKERS</div>
        <div class="stat-value" id="statTotal"><?= $totalWorkers ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">PRESENT</div>
        <div class="stat-value" id="statPresent"><?= $present ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">ABSENT</div>
        <div class="stat-value" id="statAbsent"><?= $absent ?></div>
    </div>
</div>

<!-- Filter Pills -->
<div class="filter-pills">
    <button class="filter-pill active" data-filter="all" onclick="filterEmployees('all')">Available</button>
    <button class="filter-pill" data-filter="present" onclick="filterEmployees('present')">Summary</button>
    <button class="filter-pill" data-filter="present" onclick="filterEmployees('present')">Present</button>
    <button class="filter-pill" data-filter="absent" onclick="filterEmployees('absent')">Absent</button>
</div>

<!-- Employee Search -->
<div class="employee-search-row">
    <input type="text" id="employeeSearch" class="search-box" placeholder="Search employees by name or ID..." onkeyup="searchEmployees()">
    <button class="btn-undo" onclick="undoLastAction()">
        <i class="fas fa-undo"></i> Undo
    </button>
</div>
<input type="hidden" id="attendanceDate" value="<?= $today ?>">

<!-- Employee List Container -->
<div id="employeeContainer">
    <div class="employee-placeholder">
        <i class="fas fa-users"></i>
        <p>Please select a deployment project to view all available employees</p>
    </div>
</div>

<!-- Quick Tips -->
<div class="quick-tips">
    <h3><i class="fas fa-lightbulb"></i> Quick Tips</h3>
    <div class="tips-grid">
        <div class="tip-item">
            <strong>Select a Project:</strong> Click on a project card above to view and manage its employees.
        </div>
        <div class="tip-item">
            <strong>Marking Attendance:</strong> Use the <strong>Time In</strong> button to mark present, or <strong>Mark Absent</strong> for absent employees.
        </div>
        <div class="tip-item">
            <strong>Search:</strong> You can search for specific employees by name or ID using the search box.
        </div>
        <div class="tip-item">
            <strong>Filters:</strong> Use the filter tabs to view All, Available, Present, or Absent employees.
        </div>
        <div class="tip-item">
            <strong>Date Selection:</strong> Change the date to mark attendance for a different day.
        </div>
    </div>
</div>

<script>
// Force HTTPS for API calls
if (window.location.protocol === 'http:' && window.location.hostname !== 'localhost' && window.location.hostname !== '127.0.0.1') {
    window.location.href = window.location.href.replace('http:', 'https:');
}

let currentBranchCode = null;
let currentEmployees = [];
let currentFilter = 'all';
const basePath = window.location.origin;

function selectProject(element, branchCode) {
    // Update UI
    document.querySelectorAll('.project-card').forEach(card => {
        card.classList.remove('selected');
    });
    element.classList.add('selected');
    element.classList.add('loading');
    
    currentBranchCode = branchCode;
    loadEmployees();
}

function loadEmployees() {
    if (!currentBranchCode) return;
    
    const date = document.getElementById('attendanceDate').value;
    const container = document.getElementById('employeeContainer');
    
    container.innerHTML = '<div class="employee-placeholder"><div class="loading-spinner"></div><p>Loading employees...</p></div>';
    
    fetch(`${basePath}/api/attendance/employees?branch_code=${currentBranchCode}&date=${date}`)
        .then(response => response.json())
        .then(data => {
            currentEmployees = data.employees || [];
            renderEmployees();
            updateStats();
        })
        .catch(error => {
            container.innerHTML = `<div class="employee-placeholder"><i class="fas fa-exclamation-circle"></i><p>Error loading employees: ${error.message}</p></div>`;
        })
        .finally(() => {
            document.querySelector('.project-card.selected')?.classList.remove('loading');
        });
}

let currentPage = 1;
const itemsPerPage = 10;
let lastAction = null;

function renderEmployees() {
    const container = document.getElementById('employeeContainer');
    const searchTerm = document.getElementById('employeeSearch').value.toLowerCase();
    
    // Get selected project name
    const selectedProject = document.querySelector('.project-card.selected .project-name')?.textContent || '';
    
    // Filter employees
    let filtered = currentEmployees.filter(emp => {
        const matchesSearch = !searchTerm || 
            emp.first_name.toLowerCase().includes(searchTerm) ||
            emp.last_name.toLowerCase().includes(searchTerm) ||
            emp.employee_code.toLowerCase().includes(searchTerm);
        
        const matchesFilter = currentFilter === 'all' ||
            (currentFilter === 'available' && !emp.attendance_status) ||
            (currentFilter === 'present' && (emp.attendance_status === 'present' || emp.attendance_status === 'late')) ||
            (currentFilter === 'absent' && emp.attendance_status === 'absent');
        
        return matchesSearch && matchesFilter;
    });
    
    if (filtered.length === 0) {
        container.innerHTML = '<div class="employee-placeholder"><i class="fas fa-users"></i><p>No employees found matching your criteria</p></div>';
        return;
    }
    
    // Pagination
    const totalPages = Math.ceil(filtered.length / itemsPerPage);
    const start = (currentPage - 1) * itemsPerPage;
    const paginated = filtered.slice(start, start + itemsPerPage);
    
    let html = '<div class="employee-table-container"><table class="employee-table">';
    html += '<thead><tr><th>#</th><th>Employee</th><th>Time In</th><th>Time Out</th><th>Total Hours</th><th class="actions-col">Actions</th></tr></thead><tbody>';
    
    paginated.forEach((emp, index) => {
        const rowNum = start + index + 1;
        const initials = (emp.first_name[0] + emp.last_name[0]).toUpperCase();
        const status = emp.attendance_status || 'pending';
        
        // Format time from HH:MM:SS to 24-hour format
        const formatTime = (timeStr) => {
            if (!timeStr) return '--';
            const parts = timeStr.split(':');
            const hours = parts[0];
            const minutes = parts[1];
            return `${hours}:${minutes}`;
        };
        
        const checkInTime = formatTime(emp.check_in);
        const checkOutTime = formatTime(emp.check_out);
        
        // Calculate hours
        let totalHours = '0.00';
        if (emp.check_in && emp.check_out) {
            const start = new Date(`2000-01-01T${emp.check_in}`);
            const end = new Date(`2000-01-01T${emp.check_out}`);
            const diff = (end - start) / (1000 * 60 * 60);
            totalHours = diff.toFixed(2);
        }
        
        const isPresent = status === 'present' || status === 'late';
        const isAbsent = status === 'absent';
        
        html += `
            <tr data-employee-id="${emp.id}">
                <td>${rowNum}</td>
                <td>
                    <div class="employee-cell">
                        <div class="employee-avatar">${initials}</div>
                        <div class="employee-details">
                            <h4>${emp.first_name} ${emp.last_name}</h4>
                        </div>
                    </div>
                </td>
                <td class="time-cell">${checkInTime}</td>
                <td class="time-cell">${checkOutTime}</td>
                <td class="hours-cell">${totalHours}</td>
                <td class="actions-cell">
                    ${!emp.check_in ? 
                        `<button class="btn-pill btn-timein" onclick="markAttendance(${emp.id}, 'present')">Time In</button>` :
                        !emp.check_out ?
                            `<button class="btn-pill btn-checkout" onclick="checkoutEmployee(${emp.id})">Time Out</button>` :
                            `<button class="btn-pill btn-timein" onclick="markAttendance(${emp.id}, 'present')">Time In</button>`
                    }
                </td>
            </tr>
        `;
    });
    
    html += '</tbody></table></div>';
    
    // Pagination
    if (totalPages > 1) {
        html += '<div class="pagination-row">';
        for (let i = 1; i <= Math.min(totalPages, 3); i++) {
            html += `<button class="pagination-btn ${i === currentPage ? 'active' : ''}" onclick="goToPage(${i})">${i}</button>`;
        }
        if (totalPages > 3) {
            html += '<span style="color: var(--text-secondary);">...</span>';
        }
        html += `<button class="pagination-btn next" onclick="goToPage(${Math.min(currentPage + 1, totalPages)})">Next <i class="fas fa-chevron-right" style="font-size: 0.7rem;"></i></button>`;
        html += '</div>';
    }
    
    container.innerHTML = html;
}

function goToPage(page) {
    currentPage = page;
    renderEmployees();
}

function undoLastAction() {
    if (!lastAction) {
        alert('No action to undo');
        return;
    }
    // Reverse the last action
    const reverseStatus = lastAction.previousStatus || 'pending';
    markAttendance(lastAction.employeeId, reverseStatus, true);
    lastAction = null;
}

function filterEmployees(filter) {
    currentFilter = filter;
    document.querySelectorAll('.filter-pill').forEach(pill => {
        pill.classList.toggle('active', pill.dataset.filter === filter);
    });
    currentPage = 1;
    renderEmployees();
}

function searchEmployees() {
    renderEmployees();
}

function markAttendance(employeeId, status, isUndo = false) {
    const date = document.getElementById('attendanceDate').value;

    // Find current employee status for undo tracking
    const emp = currentEmployees.find(e => e.id == employeeId);
    if (emp && !isUndo) {
        lastAction = {
            employeeId: employeeId,
            previousStatus: emp.attendance_status
        };
    }

    console.log('Marking attendance:', { employeeId, status, branch_code: currentBranchCode, date, emp });

    fetch(`${basePath}/api/attendance/mark`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            employee_id: employeeId,
            status: status,
            branch_code: currentBranchCode,
            date: date
        })
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            loadEmployees();
        } else {
            alert('Error: ' + (data.error || 'Failed to mark attendance'));
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        alert('Error: ' + error.message);
    });
}

function checkoutEmployee(employeeId) {
    // Use markAttendance with 'present' status - backend will handle checkout if already checked in
    markAttendance(employeeId, 'present');
}

function updateStats() {
    const date = document.getElementById('attendanceDate').value;
    
    fetch(`${basePath}/api/attendance/stats?date=${date}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('statTotal').textContent = data.totalWorkers;
            document.getElementById('statPresent').textContent = data.present;
            document.getElementById('statAbsent').textContent = data.absent;
        });
}

// Project search
document.getElementById('projectSearch').addEventListener('input', function() {
    const term = this.value.toLowerCase();
    document.querySelectorAll('.project-card').forEach(card => {
        const name = card.querySelector('.project-name').textContent.toLowerCase();
        card.style.display = name.includes(term) ? '' : 'none';
    });
});

// Load first project by default
window.addEventListener('DOMContentLoaded', () => {
    const firstProject = document.querySelector('.project-card');
    if (firstProject) {
        firstProject.click();
    }
});
</script>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/main.php'; ?>
