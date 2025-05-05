<?php
// Include database configuration
require_once 'config/db_config.php';

// For demonstration, we're using user_id = 1 
// In a real application, you would get this from the session after user login
$user_id = 1;

// Get current year and month
$year = isset($_GET['year']) ? intval($_GET['year']) : intval(date('Y'));
$month = isset($_GET['month']) ? intval($_GET['month']) : intval(date('m'));

// Validate year and month
if ($year < 1970 || $year > 2100) $year = date('Y');
if ($month < 1 || $month > 12) $month = date('m');

// Get the first day of the month
$first_day = mktime(0, 0, 0, $month, 1, $year);

// Get the number of days in the month
$days_in_month = date('t', $first_day);

// What day of the week the first day falls on (0 = Sunday, 1 = Monday, etc.)
$day_of_week = date('w', $first_day);

// Month names for display
$month_names = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
];

// Day names
// We'll reorder these based on user preference in JavaScript

// Standard Day names (starting with Sunday)
$day_names = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

// Get previous and next month links
$prev_month = $month - 1;
$prev_year = $year;
if ($prev_month < 1) {
    $prev_month = 12;
    $prev_year--;
}

$next_month = $month + 1;
$next_year = $year;
if ($next_month > 12) {
    $next_month = 1;
    $next_year++;
}

// Query all tasks for the current month
$start_date = "$year-$month-01";
$end_date = "$year-$month-$days_in_month";

$sql = "SELECT t.tugas_id, t.judul, t.deadline, t.kategori, t.status, mk.nama_mk 
        FROM tugas t
        JOIN mata_kuliah mk ON t.mk_id = mk.mk_id
        WHERE t.user_id = ? 
        AND DATE(t.deadline) BETWEEN ? AND ?
        ORDER BY t.deadline ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iss", $user_id, $start_date, $end_date);
$stmt->execute();
$result = $stmt->get_result();

// Organize tasks by date
$tasks_by_date = [];
while ($row = $result->fetch_assoc()) {
    $date = date('j', strtotime($row['deadline'])); // Day of month without leading zeros
    if (!isset($tasks_by_date[$date])) {
        $tasks_by_date[$date] = [];
    }
    $tasks_by_date[$date][] = $row;
}
$stmt->close();

// Get selected date for detail view (default to today if in current month, otherwise 1st of month)
$today = date('j');
$current_month = date('n');
$current_year = date('Y');

$selected_date = isset($_GET['date']) ? intval($_GET['date']) : 
    (($month == $current_month && $year == $current_year) ? $today : 1);

// Validate selected date
if ($selected_date < 1 || $selected_date > $days_in_month) {
    $selected_date = 1;
}

// Format selected date for display and queries
$selected_date_formatted = sprintf("%04d-%02d-%02d", $year, $month, $selected_date);

// Get tasks for selected date
$sql_selected = "SELECT t.*, mk.nama_mk 
                FROM tugas t
                JOIN mata_kuliah mk ON t.mk_id = mk.mk_id
                WHERE t.user_id = ? 
                AND DATE(t.deadline) = ?
                ORDER BY t.deadline ASC";
$stmt_selected = $conn->prepare($sql_selected);
$stmt_selected->bind_param("is", $user_id, $selected_date_formatted);
$stmt_selected->execute();
$selected_tasks = $stmt_selected->get_result();
$stmt_selected->close();

// Get class schedules for selected date
$day_of_week_selected = date('w', mktime(0, 0, 0, $month, $selected_date, $year));
$day_name = $day_names[$day_of_week_selected];

$sql_schedules = "SELECT jk.*, mk.nama_mk 
                FROM jadwal_kuliah jk
                JOIN mata_kuliah mk ON jk.mk_id = mk.mk_id
                WHERE jk.user_id = ? 
                AND jk.hari = ?
                ORDER BY jk.jam_mulai ASC";
$stmt_schedules = $conn->prepare($sql_schedules);
$stmt_schedules->bind_param("is", $user_id, $day_name);
$stmt_schedules->execute();
$selected_schedules = $stmt_schedules->get_result();
$stmt_schedules->close();

// Include header
include 'includes/header.php';
?>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2><i class="fas fa-calendar"></i> Kalender</h2>
            <div>
                <a href="kalender.php?month=<?php echo $prev_month; ?>&year=<?php echo $prev_year; ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-chevron-left"></i> Bulan Sebelumnya
                </a>
                <a href="kalender.php" class="btn btn-outline-primary mx-2">
                    <i class="fas fa-calendar-day"></i> Bulan Ini
                </a>
                <a href="kalender.php?month=<?php echo $next_month; ?>&year=<?php echo $next_year; ?>" class="btn btn-outline-secondary">
                    Bulan Selanjutnya <i class="fas fa-chevron-right"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0 text-center"><?php echo $month_names[$month] . ' ' . $year; ?></h4>
            </div>
            <div class="card-body">
                <table class="table table-bordered calendar-table">
                    <thead>
                        <tr>
                            <?php foreach ($day_names as $day): ?>
                                <th class="text-center"><?php echo $day; ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <?php
                            // Fill in blank days before start of month
                            for ($i = 0; $i < $day_of_week; $i++) {
                                echo '<td class="bg-light"></td>';
                            }
                            
                            // Fill in days of the month
                            for ($day = 1; $day <= $days_in_month; $day++) {
                                // Start a new row if it's the first day or if it's Sunday (day 0)
                                if (($day == 1 && $day_of_week != 0) || (($day + $day_of_week - 1) % 7 == 0 && $day != 1)) {
                                    echo '</tr><tr>';
                                }
                                
                                // Check if this day is today
                                $is_today = ($day == $today && $month == $current_month && $year == $current_year);
                                
                                // Check if this is the selected date
                                $is_selected = ($day == $selected_date);
                                
                                // Define class based on today, selected, and if there are tasks
                                $day_class = 'day-cell';
                                if ($is_today) $day_class .= ' bg-info text-white';
                                if ($is_selected) $day_class .= ' selected-day';
                                
                                echo '<td class="' . $day_class . '">';
                                echo '<a href="kalender.php?year=' . $year . '&month=' . $month . '&date=' . $day . '" 
                                        class="day-link d-block text-decoration-none ' . ($is_today ? 'text-white' : '') . '">';
                                
                                echo '<div class="d-flex justify-content-between align-items-center">';
                                echo '<span class="day-number' . ($is_selected ? ' fw-bold' : '') . '">' . $day . '</span>';
                                
                                // Show indicator if there are tasks on this day
                                if (isset($tasks_by_date[$day]) && count($tasks_by_date[$day]) > 0) {
                                    $count = count($tasks_by_date[$day]);
                                    $has_high = false;
                                    foreach ($tasks_by_date[$day] as $task) {
                                        if ($task['kategori'] == 'high') {
                                            $has_high = true;
                                            break;
                                        }
                                    }
                                    $badge_class = $has_high ? 'bg-danger' : 'bg-warning';
                                    echo '<span class="badge rounded-pill ' . $badge_class . '">' . $count . '</span>';
                                }
                                
                                echo '</div>';
                                echo '</a>';
                                echo '</td>';
                            }
                            
                            // Fill in blank days after end of month
                            $last_day_of_week = ($day_of_week + $days_in_month - 1) % 7;
                            $remaining_cells = 6 - $last_day_of_week;
                            if ($remaining_cells < 6) {
                                for ($i = 0; $i <= $remaining_cells; $i++) {
                                    echo '<td class="bg-light"></td>';
                                }
                            }
                            ?>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="fas fa-calendar-day"></i> 
                    <?php echo $day_name . ', ' . $selected_date . ' ' . $month_names[$month] . ' ' . $year; ?>
                </h5>
            </div>
            <div class="card-body">
                <h6 class="border-bottom pb-2 mb-3">Jadwal Kuliah</h6>
                <?php if ($selected_schedules->num_rows > 0): ?>
                    <div class="list-group mb-4">
                        <?php while ($schedule = $selected_schedules->fetch_assoc()): ?>
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong><?php echo htmlspecialchars($schedule['nama_mk']); ?></strong>
                                    <span class="badge bg-success">
                                        <?php 
                                        $start = date('H:i', strtotime($schedule['jam_mulai']));
                                        $end = date('H:i', strtotime($schedule['jam_selesai']));
                                        echo "$start - $end"; 
                                        ?>
                                    </span>
                                </div>
                                <?php if (!empty($schedule['lokasi'])): ?>
                                    <small class="text-muted"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($schedule['lokasi']); ?></small>
                                <?php endif; ?>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted">Tidak ada jadwal kuliah untuk hari ini.</p>
                <?php endif; ?>
                
                <h6 class="border-bottom pb-2 mb-3">Tugas</h6>
                <?php if ($selected_tasks->num_rows > 0): ?>
                    <div class="list-group">
                        <?php while ($task = $selected_tasks->fetch_assoc()): ?>
                            <a href="tugas.php" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1"><?php echo htmlspecialchars($task['judul']); ?></h6>
                                    <small class="badge bg-<?php echo ($task['kategori'] == 'high') ? 'danger' : (($task['kategori'] == 'medium') ? 'warning' : 'success'); ?>">
                                        <?php echo ucfirst($task['kategori']); ?>
                                    </small>
                                </div>
                                <p class="mb-1"><small><?php echo htmlspecialchars($task['nama_mk']); ?></small></p>
                                <small class="text-muted">
                                    <i class="far fa-clock"></i> 
                                    <?php echo date('H:i', strtotime($task['deadline'])); ?>
                                    
                                    <?php if ($task['status'] == 'completed'): ?>
                                        <span class="badge bg-primary ms-2">Selesai</span>
                                    <?php endif; ?>
                                </small>
                            </a>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted">Tidak ada tugas untuk tanggal ini.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.calendar-table th {
    background-color: #f8f9fa;
    text-align: center;
}

.calendar-table td {
    height: 80px;
    width: 14.28%;
    padding: 5px;
    vertical-align: top;
}

.day-cell {
    position: relative;
}

.day-number {
    font-size: 1.1em;
}

.selected-day {
    border: 2px solid #007bff !important;
}

.day-link {
    display: block;
    height: 100%;
    color: inherit;
}

.day-link:hover {
    background-color: rgba(0,123,255,0.1);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get calendar start day preference from localStorage
    const startDay = localStorage.getItem('startDay') || 'monday';
    
    // Reorder and update the calendar based on the start day preference
    reorderCalendar(startDay);
});

function reorderCalendar(startDay) {
    // Get the calendar table
    const calendarTable = document.querySelector('.calendar-table');
    const headerRow = calendarTable.querySelector('thead tr');
    const tbody = calendarTable.querySelector('tbody');
    
    // Day names in Indonesian
    const dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    
    // Reorder day names for header based on start day
    let reorderedDays = [...dayNames];
    if (startDay === 'monday') {
        // Move Sunday to the end if starting from Monday
        const sunday = reorderedDays.shift();
        reorderedDays.push(sunday);
    }
    
    // Update the header row
    headerRow.innerHTML = '';
    reorderedDays.forEach(day => {
        const th = document.createElement('th');
        th.textContent = day;
        th.className = 'text-center';
        headerRow.appendChild(th);
    });
    
    // Recalculate the calendar layout
    recalculateCalendarDays(startDay);
}

function recalculateCalendarDays(startDay) {
    // Get necessary PHP variables passed to JavaScript
    const year = <?php echo $year; ?>;
    const month = <?php echo $month; ?>;
    const daysInMonth = <?php echo $days_in_month; ?>;
    const firstDayOfWeek = <?php echo $day_of_week; ?>; // 0 = Sunday, 1 = Monday, etc.
    
    // Calculate offset for first day of month based on start day preference
    let dayOffset = firstDayOfWeek;
    if (startDay === 'monday') {
        // If starting from Monday, Sunday becomes day 6 instead of 0
        dayOffset = firstDayOfWeek === 0 ? 6 : firstDayOfWeek - 1;
    }
    
    // Get the table body and clear it
    const tbody = document.querySelector('.calendar-table tbody');
    tbody.innerHTML = '';
    
    // Create new rows and cells
    let row = document.createElement('tr');
    
    // Create empty cells for days before the 1st of month
    for (let i = 0; i < dayOffset; i++) {
        const td = document.createElement('td');
        td.className = 'bg-light';
        row.appendChild(td);
    }
    
    // Current date for highlighting today
    const today = new Date();
    const currentDay = today.getDate();
    const currentMonth = today.getMonth() + 1;
    const currentYear = today.getFullYear();
    
    // Selected date from URL
    const urlParams = new URLSearchParams(window.location.search);
    const selectedDate = parseInt(urlParams.get('date')) || 
        ((month == currentMonth && year == currentYear) ? currentDay : 1);
    
    // Populate days of the month
    for (let day = 1; day <= daysInMonth; day++) {
        // Calculate the day of week (0-6)
        let dayOfWeek = (dayOffset + day - 1) % 7;
        
        // Start a new row if needed
        if (day === 1 || dayOfWeek === 0) {
            if (day !== 1) {
                tbody.appendChild(row);
                row = document.createElement('tr');
            }
        }
        
        // Check if this day is today
        const isToday = (day === currentDay && month === currentMonth && year === currentYear);
        
        // Check if this is the selected date
        const isSelected = (day === selectedDate);
        
        // Create cell
        const td = document.createElement('td');
        let dayClass = 'day-cell';
        if (isToday) dayClass += ' bg-info text-white';
        if (isSelected) dayClass += ' selected-day';
        td.className = dayClass;
        
        // Create link
        const link = document.createElement('a');
        link.href = `kalender.php?year=${year}&month=${month}&date=${day}`;
        link.className = `day-link d-block text-decoration-none ${isToday ? 'text-white' : ''}`;
        
        // Day number and indicators container
        const dayContainer = document.createElement('div');
        dayContainer.className = 'd-flex justify-content-between align-items-center';
        
        // Day number
        const dayNumber = document.createElement('span');
        dayNumber.className = `day-number ${isSelected ? 'fw-bold' : ''}`;
        dayNumber.textContent = day;
        dayContainer.appendChild(dayNumber);
        
        // Task indicators (if any)
        <?php if (!empty($tasks_by_date)): ?>
        const tasksByDate = <?php echo json_encode($tasks_by_date); ?>;
        if (tasksByDate[day] && tasksByDate[day].length > 0) {
            const count = tasksByDate[day].length;
            let hasHighPriority = false;
            
            // Check if any task has high priority
            for (const task of tasksByDate[day]) {
                if (task.kategori === 'high') {
                    hasHighPriority = true;
                    break;
                }
            }
            
            const badge = document.createElement('span');
            badge.className = `badge rounded-pill ${hasHighPriority ? 'bg-danger' : 'bg-warning'}`;
            badge.textContent = count;
            dayContainer.appendChild(badge);
        }
        <?php endif; ?>
        
        link.appendChild(dayContainer);
        td.appendChild(link);
        row.appendChild(td);
    }
    
    // Add empty cells at the end if needed
    const lastDayOfWeek = (dayOffset + daysInMonth) % 7;
    if (lastDayOfWeek !== 0) {
        const emptyCells = 7 - lastDayOfWeek;
        for (let i = 0; i < emptyCells; i++) {
            const td = document.createElement('td');
            td.className = 'bg-light';
            row.appendChild(td);
        }
    }
    
    // Append the last row
    tbody.appendChild(row);
}
</script>

<?php
// Include footer
include 'includes/footer.php';
?>