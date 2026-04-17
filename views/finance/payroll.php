<?php
$title = 'Weekly Payroll Report';
$currentPage = 'payroll';

// Get data from controller
$branches = $branches ?? [];
$currentBranch = $currentBranch ?? '';
$weekStart = $weekStart ?? date('Y-m-d');
$weekEnd = $weekEnd ?? date('Y-m-d');
$weekNumber = $weekNumber ?? 1;

ob_start();
?>

<style>
    .payroll-header {
        margin-bottom: 24px;
    }

    .payroll-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 8px;
    }

    .payroll-subtitle {
        color: var(--text-secondary);
        font-size: 0.95rem;
    }

    .payroll-controls {
        display: flex;
        gap: 16px;
        margin-bottom: 24px;
        flex-wrap: wrap;
    }

    .control-group {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .control-label {
        font-size: 0.85rem;
        color: var(--text-secondary);
        font-weight: 500;
    }

    .control-select, .control-input {
        padding: 8px 12px;
        border: 1px solid var(--border-color);
        border-radius: 6px;
        background: var(--bg-secondary);
        color: var(--text-primary);
        font-size: 0.9rem;
    }

    .week-nav {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .week-nav button {
        padding: 8px 12px;
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: 6px;
        color: var(--text-primary);
        cursor: pointer;
        transition: all 0.2s;
    }

    .week-nav button:hover {
        background: var(--accent-color);
        color: #000;
    }

    .week-display {
        font-weight: 600;
        color: var(--accent-color);
        padding: 8px 16px;
        background: rgba(255, 215, 0, 0.1);
        border-radius: 6px;
    }

    .payroll-actions {
        display: flex;
        gap: 12px;
        margin-bottom: 24px;
    }

    .btn {
        padding: 10px 20px;
        border-radius: 8px;
        font-size: 0.9rem;
        font-weight: 500;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: none;
        transition: all 0.2s;
    }

    .btn-primary {
        background: var(--accent-color);
        color: #000;
    }

    .btn-primary:hover {
        background: #e6c200;
    }

    .btn-secondary {
        background: var(--bg-secondary);
        color: var(--text-primary);
        border: 1px solid var(--border-color);
    }

    .btn-secondary:hover {
        background: var(--border-color);
    }

    .payroll-container {
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        overflow: hidden;
    }

    .payroll-table-container {
        overflow-x: auto;
    }

    .payroll-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.85rem;
    }

    .payroll-table th {
        background: var(--bg-tertiary);
        color: var(--text-secondary);
        font-weight: 600;
        text-align: left;
        padding: 12px;
        border-bottom: 1px solid var(--border-color);
        white-space: nowrap;
    }

    .payroll-table td {
        padding: 12px;
        border-bottom: 1px solid var(--border-color);
        vertical-align: middle;
    }

    .payroll-table tr:hover {
        background: rgba(255, 215, 0, 0.05);
    }

    .employee-info {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .employee-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: var(--accent-color);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.75rem;
        color: #000;
    }

    .employee-name {
        font-weight: 500;
        color: var(--text-primary);
    }

    .employee-code {
        font-size: 0.75rem;
        color: var(--text-secondary);
    }

    .week-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 500;
        background: rgba(255, 215, 0, 0.2);
        color: var(--accent-color);
    }

    .days-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 500;
        background: rgba(59, 130, 246, 0.2);
        color: #3b82f6;
    }

    .currency {
        font-family: 'Courier New', monospace;
        font-weight: 500;
    }

    .deductions-expand {
        cursor: pointer;
        color: var(--text-secondary);
        font-size: 0.8rem;
    }

    .deductions-expand:hover {
        color: var(--accent-color);
    }

    .deductions-detail {
        display: none;
        position: absolute;
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        z-index: 100;
        min-width: 180px;
    }

    .deductions-detail.show {
        display: block;
    }

    .deduction-item {
        display: flex;
        justify-content: space-between;
        padding: 4px 0;
        font-size: 0.8rem;
    }

    .deduction-item.total {
        border-top: 1px solid var(--border-color);
        margin-top: 8px;
        padding-top: 8px;
        font-weight: 600;
    }

    .net-pay {
        font-weight: 600;
        color: var(--accent-color);
    }

    .gross-pay {
        font-weight: 600;
        color: #22c55e;
    }

    .no-deduction {
        color: var(--text-secondary);
        font-style: italic;
    }

    .btn-payslip {
        padding: 6px 12px;
        background: rgba(255, 215, 0, 0.1);
        color: var(--accent-color);
        border: 1px solid var(--accent-color);
        border-radius: 6px;
        font-size: 0.8rem;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: all 0.2s;
    }

    .btn-payslip:hover {
        background: var(--accent-color);
        color: #000;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
    }

    .empty-state i {
        font-size: 3rem;
        color: var(--text-secondary);
        margin-bottom: 16px;
    }

    .empty-state h3 {
        color: var(--text-primary);
        margin-bottom: 8px;
    }

    .empty-state p {
        color: var(--text-secondary);
    }

    .loading {
        text-align: center;
        padding: 40px;
    }

    .loading-spinner {
        width: 40px;
        height: 40px;
        border: 3px solid var(--border-color);
        border-top-color: var(--accent-color);
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin: 0 auto 16px;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* Modal Styles */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.7);
        z-index: 1000;
        justify-content: center;
        align-items: center;
    }

    .modal-overlay.show {
        display: flex;
    }

    .modal-content {
        background: var(--bg-secondary);
        border-radius: 12px;
        max-width: 600px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
        border: 1px solid var(--border-color);
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 24px;
        border-bottom: 1px solid var(--border-color);
    }

    .modal-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--text-primary);
    }

    .modal-close {
        background: none;
        border: none;
        color: var(--text-secondary);
        font-size: 1.5rem;
        cursor: pointer;
        padding: 0;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
    }

    .modal-close:hover {
        background: var(--bg-tertiary);
        color: var(--text-primary);
    }

    .modal-body {
        padding: 24px;
    }

    /* Payslip Styles */
    .payslip-container {
        font-family: 'Arial', sans-serif;
    }

    .payslip-header {
        text-align: center;
        margin-bottom: 24px;
        padding-bottom: 20px;
        border-bottom: 2px solid var(--accent-color);
    }

    .payslip-company {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 4px;
    }

    .payslip-title {
        font-size: 1.1rem;
        color: var(--text-secondary);
        margin-bottom: 8px;
    }

    .payslip-period {
        font-size: 0.9rem;
        color: var(--accent-color);
        font-weight: 500;
    }

    .payslip-section {
        margin-bottom: 20px;
    }

    .payslip-section-title {
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 12px;
        padding-bottom: 8px;
        border-bottom: 1px solid var(--border-color);
    }

    .payslip-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }

    .payslip-item {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        font-size: 0.9rem;
    }

    .payslip-item.total {
        font-weight: 600;
        color: var(--text-primary);
        border-top: 1px solid var(--border-color);
        margin-top: 8px;
        padding-top: 12px;
    }

    .payslip-item.net {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--accent-color);
        background: rgba(255, 215, 0, 0.1);
        padding: 16px;
        border-radius: 8px;
        margin-top: 12px;
    }

    .payslip-label {
        color: var(--text-secondary);
    }

    .payslip-value {
        color: var(--text-primary);
        font-weight: 500;
    }

    .payslip-value.positive {
        color: #22c55e;
    }

    .payslip-value.negative {
        color: #ef4444;
    }

    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        padding: 16px 24px;
        border-top: 1px solid var(--border-color);
    }

    @media print {
        .modal-overlay {
            position: static;
            display: block;
            background: white;
        }
        .modal-content {
            max-width: 100%;
            border: none;
        }
        .modal-header, .modal-footer {
            display: none;
        }
        .payslip-container {
            color: black;
        }
    }

    @media (max-width: 768px) {
        .payroll-controls {
            flex-direction: column;
        }
        .payroll-table th,
        .payroll-table td {
            padding: 8px;
            font-size: 0.8rem;
        }
        .employee-info {
            flex-direction: column;
            align-items: flex-start;
            gap: 4px;
        }
    }
</style>

<div class="payroll-header">
    <h1 class="payroll-title">Weekly Payroll Report</h1>
    <p class="payroll-subtitle">Manage employee payroll for Monday-Saturday work weeks</p>
</div>

<div class="payroll-controls">
    <div class="control-group">
        <label class="control-label">Week:</label>
        <div class="week-nav">
            <button onclick="changeWeek(-1)"><i class="fas fa-chevron-left"></i></button>
            <span class="week-display" id="weekDisplay">Week <?= $weekNumber ?>: <?= date('M d', strtotime($weekStart)) ?> - <?= date('M d', strtotime($weekEnd)) ?></span>
            <button onclick="changeWeek(1)"><i class="fas fa-chevron-right"></i></button>
        </div>
    </div>

    <div class="control-group">
        <label class="control-label">Branch:</label>
        <select class="control-select" id="branchSelect" onchange="loadPayroll()">
            <?php foreach ($branches as $branch): ?>
            <option value="<?= htmlspecialchars($branch['branch_code']) ?>" <?= $branch['branch_code'] === $currentBranch ? 'selected' : '' ?>>
                <?= htmlspecialchars($branch['branch_name']) ?> (<?= htmlspecialchars($branch['branch_code']) ?>)
            </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="control-group">
        <label class="control-label">Year:</label>
        <select class="control-select" id="yearSelect" onchange="updateWeekOptions()">
            <?php for ($y = date('Y'); $y >= date('Y') - 2; $y--): ?>
            <option value="<?= $y ?>" <?= $y == date('Y') ? 'selected' : '' ?>><?= $y ?></option>
            <?php endfor; ?>
        </select>
    </div>

    <div class="control-group">
        <label class="control-label">Month:</label>
        <select class="control-select" id="monthSelect" onchange="updateWeekOptions()">
            <?php for ($m = 1; $m <= 12; $m++): ?>
            <option value="<?= $m ?>" <?= $m == date('n') ? 'selected' : '' ?>><?= date('F', mktime(0, 0, 0, $m, 1)) ?></option>
            <?php endfor; ?>
        </select>
    </div>
</div>

<div class="payroll-actions">
    <button class="btn btn-primary" onclick="calculatePayroll()">
        <i class="fas fa-calculator"></i>
        Calculate Payroll
    </button>
    <button class="btn btn-secondary" onclick="exportPayroll()">
        <i class="fas fa-download"></i>
        Export to Excel
    </button>
</div>

<div class="payroll-container">
    <div class="payroll-table-container">
        <table class="payroll-table">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Week</th>
                    <th>Days</th>
                    <th>Daily Rate</th>
                    <th>Basic Pay</th>
                    <th>Performance</th>
                    <th>Gross Pay</th>
                    <th>Deductions</th>
                    <th>Net Pay</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="payrollTableBody">
                <tr>
                    <td colspan="10" class="loading">
                        <div class="loading-spinner"></div>
                        <p>Loading payroll data...</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Payslip Modal -->
<div class="modal-overlay" id="payslipModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title"><i class="fas fa-file-invoice"></i> Employee Payslip</h2>
            <button class="modal-close" onclick="closePayslip()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="payslip-container" id="payslipContent">
                <!-- Payslip content will be injected here -->
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closePayslip()">Close</button>
            <button class="btn btn-primary" onclick="printPayslip()">
                <i class="fas fa-print"></i> Print
            </button>
        </div>
    </div>
</div>

<script>
let currentWeekStart = '<?= $weekStart ?>';
let currentBranch = '<?= $currentBranch ?>';
let payrollData = [];

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    loadPayroll();
});

function changeWeek(direction) {
    const newDate = new Date(currentWeekStart);
    newDate.setDate(newDate.getDate() + (direction * 7));
    currentWeekStart = newDate.toISOString().split('T')[0];

    // Get week number
    const day = newDate.getDate();
    const weekNumber = Math.ceil(day / 7);

    // Update display
    const weekEnd = new Date(newDate);
    weekEnd.setDate(weekEnd.getDate() + 5);

    document.getElementById('weekDisplay').textContent =
        `Week ${weekNumber}: ${formatDate(newDate)} - ${formatDate(weekEnd)}`;

    loadPayroll();
}

function formatDate(date) {
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
}

function loadPayroll() {
    currentBranch = document.getElementById('branchSelect').value;

    const tbody = document.getElementById('payrollTableBody');
    tbody.innerHTML = `
        <tr>
            <td colspan="10" class="loading">
                <div class="loading-spinner"></div>
                <p>Loading payroll data...</p>
            </td>
        </tr>
    `;

    fetch(`/api/payroll/weekly?week_start=${currentWeekStart}&branch_code=${currentBranch}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                return response.text().then(text => {
                    throw new Error('Server returned non-JSON response. Check server logs.');
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                payrollData = data.payroll;
                renderPayrollTable(payrollData);
            } else {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="10" class="empty-state">
                            <i class="fas fa-exclamation-circle"></i>
                            <h3>Error loading payroll</h3>
                            <p>${data.error || 'Please try again'}</p>
                        </td>
                    </tr>
                `;
            }
        })
        .catch(error => {
            console.error('Payroll fetch error:', error);
            tbody.innerHTML = `
                <tr>
                    <td colspan="10" class="empty-state">
                        <i class="fas fa-exclamation-circle"></i>
                        <h3>Error loading payroll</h3>
                        <p>${error.message}</p>
                    </td>
                </tr>
            `;
        });
}

function renderPayrollTable(data) {
    const tbody = document.getElementById('payrollTableBody');

    if (data.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="10" class="empty-state">
                    <i class="fas fa-users-slash"></i>
                    <h3>No employees found</h3>
                    <p>No active employees in this branch for the selected week.</p>
                </td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = data.map(emp => {
        const hasDeduction = emp.has_deduction == 1;
        const deductionsHtml = hasDeduction && emp.week_number < 4 ? `
            <div class="deductions-expand" onclick="toggleDeductions(this)">
                <i class="fas fa-eye"></i> View
            </div>
            <div class="deductions-detail">
                <div class="deduction-item"><span>SSS:</span><span>₱${formatNumber(emp.sss_contribution)}</span></div>
                <div class="deduction-item"><span>PhilHealth:</span><span>₱${formatNumber(emp.phic_contribution)}</span></div>
                <div class="deduction-item"><span>Pag-IBIG:</span><span>₱${formatNumber(emp.hdmf_contribution)}</span></div>
                <div class="deduction-item total"><span>Total:</span><span>₱${formatNumber(emp.total_deductions)}</span></div>
            </div>
        ` : '<span class="no-deduction">-</span>';

        return `
            <tr>
                <td>
                    <div class="employee-info">
                        <div class="employee-avatar">${getInitials(emp.first_name, emp.last_name)}</div>
                        <div>
                            <div class="employee-name">${emp.full_name}</div>
                            <div class="employee-code">${emp.employee_code}</div>
                        </div>
                    </div>
                </td>
                <td><span class="week-badge">Week ${emp.week_number}</span></td>
                <td><span class="days-badge"><i class="fas fa-calendar-check"></i> ${emp.days_worked}</span></td>
                <td class="currency">₱${formatNumber(emp.daily_rate)}</td>
                <td class="currency">₱${formatNumber(emp.basic_pay)}</td>
                <td class="currency">₱${formatNumber(emp.performance_allowance)}</td>
                <td class="currency gross-pay">₱${formatNumber(emp.gross_pay)}</td>
                <td style="position: relative;">${deductionsHtml}</td>
                <td class="currency net-pay">₱${formatNumber(emp.net_pay)}</td>
                <td>
                    <button class="btn-payslip" onclick="showPayslip(${emp.employee_id})">
                        <i class="fas fa-file-invoice"></i> Payslip
                    </button>
                </td>
            </tr>
        `;
    }).join('');
}

function getInitials(first, last) {
    return (first?.[0] || '') + (last?.[0] || '');
}

function formatNumber(num) {
    return parseFloat(num).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function toggleDeductions(element) {
    const detail = element.nextElementSibling;
    const isShowing = detail.classList.contains('show');

    // Hide all other deduction details
    document.querySelectorAll('.deductions-detail').forEach(d => d.classList.remove('show'));

    if (!isShowing) {
        detail.classList.add('show');
    }
}

function calculatePayroll() {
    const btn = document.querySelector('.btn-primary');
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Calculating...';
    btn.disabled = true;

    fetch('/api/payroll/calculate', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            week_start: currentWeekStart,
            branch_code: currentBranch
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            payrollData = data.data.employees;
            renderPayrollTable(payrollData);
            alert(`Payroll calculated successfully! Saved ${data.save_results.saved} of ${data.save_results.total} records.`);
        } else {
            alert('Error: ' + (data.error || 'Failed to calculate payroll'));
        }
    })
    .catch(error => {
        alert('Error: ' + error.message);
    })
    .finally(() => {
        btn.innerHTML = '<i class="fas fa-calculator"></i> Calculate Payroll';
        btn.disabled = false;
    });
}

function exportPayroll() {
    window.location.href = `/api/payroll/export?week_start=${currentWeekStart}&branch_code=${currentBranch}`;
}

function showPayslip(employeeId) {
    const emp = payrollData.find(e => e.employee_id == employeeId);
    if (!emp) return;

    const hasDeduction = emp.has_deduction == 1 && emp.week_number < 4;

    document.getElementById('payslipContent').innerHTML = `
        <div class="payslip-header">
            <div class="payslip-company">JAJR Security Agency</div>
            <div class="payslip-title">Employee Payslip</div>
            <div class="payslip-period">Week ${emp.week_number}: ${formatDateDisplay(emp.payroll_week_start)} - ${formatDateDisplay(emp.payroll_week_end)}</div>
        </div>

        <div class="payslip-section">
            <div class="payslip-section-title">Employee Information</div>
            <div class="payslip-grid">
                <div class="payslip-item">
                    <span class="payslip-label">Name:</span>
                    <span class="payslip-value">${emp.full_name}</span>
                </div>
                <div class="payslip-item">
                    <span class="payslip-label">Employee Code:</span>
                    <span class="payslip-value">${emp.employee_code}</span>
                </div>
                <div class="payslip-item">
                    <span class="payslip-label">Branch:</span>
                    <span class="payslip-value">${emp.branch_code}</span>
                </div>
                <div class="payslip-item">
                    <span class="payslip-label">Department:</span>
                    <span class="payslip-value">${emp.department || 'N/A'}</span>
                </div>
            </div>
        </div>

        <div class="payslip-section">
            <div class="payslip-section-title">Attendance & Earnings</div>
            <div class="payslip-item">
                <span class="payslip-label">Days Worked:</span>
                <span class="payslip-value">${emp.days_worked} days (Mon-Sat)</span>
            </div>
            <div class="payslip-item">
                <span class="payslip-label">Daily Rate:</span>
                <span class="payslip-value">₱${formatNumber(emp.daily_rate)}</span>
            </div>
            <div class="payslip-item">
                <span class="payslip-label">Basic Pay:</span>
                <span class="payslip-value positive">₱${formatNumber(emp.basic_pay)}</span>
            </div>
            <div class="payslip-item">
                <span class="payslip-label">Performance Allowance:</span>
                <span class="payslip-value positive">₱${formatNumber(emp.performance_allowance)}</span>
            </div>
            <div class="payslip-item total">
                <span class="payslip-label">Gross Pay:</span>
                <span class="payslip-value positive">₱${formatNumber(emp.gross_pay)}</span>
            </div>
        </div>

        <div class="payslip-section">
            <div class="payslip-section-title">Deductions ${!hasDeduction ? '(None for this week)' : ''}</div>
            ${hasDeduction ? `
            <div class="payslip-item">
                <span class="payslip-label">SSS Contribution:</span>
                <span class="payslip-value negative">₱${formatNumber(emp.sss_contribution)}</span>
            </div>
            <div class="payslip-item">
                <span class="payslip-label">PhilHealth:</span>
                <span class="payslip-value negative">₱${formatNumber(emp.phic_contribution)}</span>
            </div>
            <div class="payslip-item">
                <span class="payslip-label">Pag-IBIG:</span>
                <span class="payslip-value negative">₱${formatNumber(emp.hdmf_contribution)}</span>
            </div>
            ` : '<div class="payslip-item"><span class="payslip-label">No deductions applicable</span></div>'}
            <div class="payslip-item total">
                <span class="payslip-label">Total Deductions:</span>
                <span class="payslip-value negative">₱${formatNumber(emp.total_deductions)}</span>
            </div>
        </div>

        <div class="payslip-item net">
            <span class="payslip-label">NET PAY:</span>
            <span class="payslip-value">₱${formatNumber(emp.net_pay)}</span>
        </div>
    `;

    document.getElementById('payslipModal').classList.add('show');
}

function formatDateDisplay(dateStr) {
    const date = new Date(dateStr);
    return date.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
}

function closePayslip() {
    document.getElementById('payslipModal').classList.remove('show');
}

function printPayslip() {
    window.print();
}

// Close modal when clicking outside
document.getElementById('payslipModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closePayslip();
    }
});

// Close deduction details when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('.deductions-expand') && !e.target.closest('.deductions-detail')) {
        document.querySelectorAll('.deductions-detail').forEach(d => d.classList.remove('show'));
    }
});
</script>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/finance_layout.php'; ?>
