<?php
require_once "db.php";
$initialStudents = [];
try {
    $initialStudents = $pdo->query("SELECT id, name, email, mobile, course, year, gender, status FROM students ORDER BY id DESC LIMIT 15")->fetchAll();
} catch (PDOException $e) {
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>StudentHub | Live AJAX & Fetch CRUD (Practical 13)</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h1><span class="title-badge">StudentHub Portal</span></h1>
    <nav class="ttt">
<a href="home.html" class="m">Home</a>
<a href="about.html" class="m1">About</a>
<a href="ajax_crud.php" class="m3">AJAX Live CRUD</a>
<a href="dashboard.html" class="m4">Dashboard</a>
<a href="events.html" class="m5">Events</a>
<a href="profile.html" class="m6">Profile</a>
<a href="contact.html" class="m7">Contact</a>
<a href="faq.html" class="m8">FAQ</a>
<a href="feedback.html" class="m8">Feedback</a>
    </nav>
</header>

<div class="page-box">
    <h2>Asynchronous CRUD (AJAX & Fetch API)</h2>
    <p style="text-align:center; color:#555; margin-bottom:20px;">Practical 13: Live search, asynchronous form submission, and dynamic DOM updates without page refresh</p>

    <div id="toastAlert" style="display:none; padding:12px 18px; border-radius:4px; margin-bottom:20px; font-weight:bold;"></div>

    <div style="background:#ffffff; border:1px solid #dbe3ee; border-radius:6px; padding:20px; margin-bottom:25px;">
        <h3 style="margin-bottom:15px; color:#1f4e8c;">+ Add Student Asynchronously (Fetch API)</h3>
        <form id="ajaxAddForm" style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:12px; align-items:end;">
            <div>
                <label style="display:block; font-size:13px; font-weight:bold; margin-bottom:3px;">Full Name:</label>
                <input type="text" id="addName" style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px;" placeholder="Full Name" required>
            </div>
            <div>
                <label style="display:block; font-size:13px; font-weight:bold; margin-bottom:3px;">Email Address:</label>
                <input type="email" id="addEmail" style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px;" placeholder="Email" required>
            </div>
            <div>
                <label style="display:block; font-size:13px; font-weight:bold; margin-bottom:3px;">Mobile:</label>
                <input type="text" id="addMobile" style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px;" placeholder="10 Digits">
            </div>
            <div>
                <label style="display:block; font-size:13px; font-weight:bold; margin-bottom:3px;">Course:</label>
                <select id="addCourse" style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px;" required>
                    <option value="Computer Engineering">Computer Engineering</option>
                    <option value="Information Technology">Information Technology</option>
                    <option value="Civil Engineering">Civil Engineering</option>
                    <option value="Mechanical Engineering">Mechanical Engineering</option>
                </select>
            </div>
            <div>
                <label style="display:block; font-size:13px; font-weight:bold; margin-bottom:3px;">Year:</label>
                <select id="addYear" style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px;" required>
                    <option value="First Year">First Year</option>
                    <option value="Second Year">Second Year</option>
                    <option value="Third Year">Third Year</option>
                    <option value="Fourth Year">Fourth Year</option>
                </select>
            </div>
            <div>
                <button type="submit" class="btn btn-primary" style="width:100%; padding:8px 16px;">Add via Fetch</button>
            </div>
        </form>
    </div>

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px; flex-wrap:wrap; gap:10px;">
        <div style="flex:1; min-width:250px;">
            <input type="text" id="liveSearchInput" placeholder="Type to live search students instantly..." style="width:100%; max-width:400px; padding:9px 12px; border:1px solid #ccc; border-radius:4px;">
        </div>
        <div id="liveCountText" style="color:#555; font-size:14px;">Showing <?php echo count($initialStudents); ?> students</div>
    </div>

    <div class="table-container">
        <table class="record-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Course</th>
                    <th>Year</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="studentTableBody">
                <?php foreach ($initialStudents as $s): ?>
                    <tr id="row-<?php echo $s["id"]; ?>">
                        <td><?php echo $s["id"]; ?></td>
                        <td><strong><?php echo htmlspecialchars($s["name"]); ?></strong></td>
                        <td><?php echo htmlspecialchars($s["email"]); ?></td>
                        <td><?php echo htmlspecialchars($s["mobile"] ?? "N/A"); ?></td>
                        <td><?php echo htmlspecialchars($s["course"]); ?></td>
                        <td><?php echo htmlspecialchars($s["year"]); ?></td>
                        <td><span class="badge badge-success"><?php echo htmlspecialchars(ucfirst($s["status"])); ?></span></td>
                        <td>
                            <button class="btn-small btn-danger" onclick="deleteStudent(<?php echo $s["id"]; ?>)">Delete (AJAX)</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<footer>
    <p>2026 StudentHub Portal. All rights reserved.</p>
</footer>

<script>
var searchDebounceTimer = null;
var searchInput = document.getElementById("liveSearchInput");
var tableBody = document.getElementById("studentTableBody");
var countText = document.getElementById("liveCountText");
var addForm = document.getElementById("ajaxAddForm");
var toast = document.getElementById("toastAlert");

function showToast(message, isSuccess) {
    toast.textContent = message;
    toast.style.display = "block";
    if (isSuccess) {
        toast.style.background = "#e8f5e9";
        toast.style.color = "#2e7d32";
        toast.style.border = "1px solid #c8e6c9";
    } else {
        toast.style.background = "#ffebee";
        toast.style.color = "#c62828";
        toast.style.border = "1px solid #ffcdd2";
    }
    setTimeout(function() {
        toast.style.display = "none";
    }, 4000);
}

function renderTable(records) {
    tableBody.innerHTML = "";
    if (!records || records.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="8" style="text-align:center; padding:20px; color:#888;">No students match the search query.</td></tr>';
        countText.textContent = "Showing 0 students";
        return;
    }
    countText.textContent = "Showing " + records.length + " students";
    records.forEach(function(s) {
        var tr = document.createElement("tr");
        tr.id = "row-" + s.id;
        tr.innerHTML = '<td>' + s.id + '</td>' +
            '<td><strong>' + escapeHtml(s.name) + '</strong></td>' +
            '<td>' + escapeHtml(s.email) + '</td>' +
            '<td>' + escapeHtml(s.mobile || "N/A") + '</td>' +
            '<td>' + escapeHtml(s.course) + '</td>' +
            '<td>' + escapeHtml(s.year) + '</td>' +
            '<td><span class="badge badge-success">' + escapeHtml(s.status) + '</span></td>' +
            '<td><button class="btn-small btn-danger" onclick="deleteStudent(' + s.id + ')">Delete (AJAX)</button></td>';
        tableBody.appendChild(tr);
    });
}

function escapeHtml(text) {
    if (!text) return "";
    var div = document.createElement("div");
    div.textContent = text;
    return div.innerHTML;
}

searchInput.addEventListener("input", function() {
    clearTimeout(searchDebounceTimer);
    var query = searchInput.value.trim();
    searchDebounceTimer = setTimeout(function() {
        fetch("api.php?action=search&q=" + encodeURIComponent(query))
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (data.status === "success") {
                    renderTable(data.data);
                }
            })
            .catch(function(err) {
                showToast("Live search request failed: " + err, false);
            });
    }, 300);
});

addForm.addEventListener("submit", function(e) {
    e.preventDefault();
    var payload = {
        name: document.getElementById("addName").value.trim(),
        email: document.getElementById("addEmail").value.trim(),
        mobile: document.getElementById("addMobile").value.trim(),
        course: document.getElementById("addCourse").value,
        year: document.getElementById("addYear").value,
        gender: "Male"
    };

    fetch("api.php?action=create", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload)
    })
    .then(function(res) { return res.json(); })
    .then(function(res) {
        if (res.status === "success") {
            showToast(res.message, true);
            addForm.reset();
            var tr = document.createElement("tr");
            tr.id = "row-" + res.data.id;
            tr.innerHTML = '<td>' + res.data.id + '</td>' +
                '<td><strong>' + escapeHtml(res.data.name) + '</strong></td>' +
                '<td>' + escapeHtml(res.data.email) + '</td>' +
                '<td>' + escapeHtml(res.data.mobile || "N/A") + '</td>' +
                '<td>' + escapeHtml(res.data.course) + '</td>' +
                '<td>' + escapeHtml(res.data.year) + '</td>' +
                '<td><span class="badge badge-success">active</span></td>' +
                '<td><button class="btn-small btn-danger" onclick="deleteStudent(' + res.data.id + ')">Delete (AJAX)</button></td>';
            tableBody.insertBefore(tr, tableBody.firstChild);
        } else {
            showToast(res.message || "Error adding student.", false);
        }
    })
    .catch(function(err) {
        showToast("Network error: " + err, false);
    });
});

function deleteStudent(id) {
    if (!confirm("Are you sure you want to delete student #" + id + " via AJAX?")) {
        return;
    }
    fetch("api.php?action=delete", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ id: id })
    })
    .then(function(res) { return res.json(); })
    .then(function(res) {
        if (res.status === "success") {
            showToast(res.message, true);
            var row = document.getElementById("row-" + id);
            if (row) {
                row.style.transition = "opacity 0.4s";
                row.style.opacity = "0";
                setTimeout(function() { row.remove(); }, 400);
            }
        } else {
            showToast(res.message || "Failed to delete student.", false);
        }
    })
    .catch(function(err) {
        showToast("Delete request failed: " + err, false);
    });
}
</script>

</body>
</html>
