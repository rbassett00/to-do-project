<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: views/login.php");
    exit();
}

$mysql_servername = getenv("MYSQL_SERVERNAME");
$mysql_user = getenv("MYSQL_USER");
$mysql_password = getenv("MYSQL_PASSWORD");
$mysql_database = getenv("MYSQL_DATABASE");
$conn = new mysqli($mysql_servername, $mysql_user, $mysql_password, $mysql_database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['id'];
$filter = isset($_GET['filter']) && $_GET['filter'] == '1';
$sort = isset($_GET['sort']) && $_GET['sort'] == '1'; // Check if the sort parameter is set

if ($filter) {
    if ($sort) {
        $stmt = $conn->prepare("SELECT id, text, date, done FROM task WHERE user_id = ? AND done = 0 ORDER BY date ASC");
    } else {
        $stmt = $conn->prepare("SELECT id, text, date, done FROM task WHERE user_id = ? AND done = 0 ORDER BY id ASC"); // Default order
    }
} else {
    if ($sort) {
        $stmt = $conn->prepare("SELECT id, text, date, done FROM task WHERE user_id = ? ORDER BY date ASC");
    } else {
        $stmt = $conn->prepare("SELECT id, text, date, done FROM task WHERE user_id = ? ORDER BY id ASC"); // Default order
    }
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$tasks = $result->fetch_all(MYSQLI_ASSOC);

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>To-Do</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">

  <script>
    document.addEventListener("DOMContentLoaded", function() {
        // Handle the sort toggle
        let sortCheckbox = document.getElementById("cb-sort");

        sortCheckbox.addEventListener("change", function() {
            let sortValue = this.checked ? "1" : "0";
            let url = new URL(window.location.href);
            url.searchParams.set("sort", sortValue); // Add 'sort' to the query params
            window.location.href = url.toString(); // Reload with the new URL
        });

        // Handle the filter toggle
        let filterCheckbox = document.getElementById("filter");

        filterCheckbox.addEventListener("change", function() {
            let filterValue = this.checked ? "1" : "0";
            let url = new URL(window.location.href);
            url.searchParams.set("filter", filterValue);
            window.location.href = url.toString();
        });

        // Update task style when marked as done
        document.querySelectorAll(".task-done").forEach(checkbox => {
            updateTaskStyle(checkbox);

            checkbox.addEventListener("change", function() {
                updateTaskStyle(this);
            });
        });

        function updateTaskStyle(checkbox) {
            let taskText = checkbox.closest(".task").querySelector(".task-description");
            if (checkbox.checked) {
                taskText.style.textDecoration = "line-through";
                taskText.style.color = "gray"; // Optional: change color for clarity
            } else {
                taskText.style.textDecoration = "none";
                taskText.style.color = ""; // Reset to default color
            }
        }
    });
  </script>

</head>

<body>
  <nav>
    <form action="actions/logout_action.php" method="post" style="display:inline;">
        <button type="submit" class="create-task">Logout</button>
    </form>
  </nav>

  <div class="main-panel">
    <h1>To-Do</h1>

    <!-- Sort by date toggle -->
    <input type='checkbox' class="toggle-switch" id="cb-sort" <?= isset($_GET['sort']) && $_GET['sort'] == '1' ? 'checked' : '' ?> />
    <label for="cb-sort">Sort by date</label>

    <!-- Filter toggle -->
    <input type='checkbox' class="toggle-switch" id="filter" <?= isset($_GET['filter']) && $_GET['filter'] == '1' ? 'checked' : '' ?> />
    <label for="filter">Filter</label>

    <div class="task-container">
      <ul class="tasklist">
        <?php foreach ($tasks as $task): ?>
          <li class="task">
            <form action="actions/update_action.php" method="post" style="display:inline;">
              <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
              <input type="checkbox" name="done" class="task-done checkbox-icon"
              value="1" <?= $task['done'] ? 'checked' : '' ?> onchange="this.form.submit();">
            </form>
            <span class="task-description"><?= htmlspecialchars($task['text']) ?></span>
            <span class="task-date"><?= htmlspecialchars($task['date']) ?></span>
            <form action="actions/delete_action.php" method="post" style="display:inline;">
              <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
              <button type="submit" class="task-delete material-icon">delete</button>
            </form>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>

    <div class="form-box">
      <form class="form-create-task" action="actions/create_action.php" method="post">
        <input type="text" name="description" class="my-description" required placeholder="add task description">
        <input type="date" class="my-date" name="date" required>
        <p></p>
        <button class="create-task">CREATE TASK</button>
      </form>
    </div>
  </div>

  <div class="stem stem1"></div>
  <div class="stem stem2"></div>
  <div class="stem stem3"></div>
  <div class="stem stem4"></div>
  <div class="stem stem5"></div>
  <div class="stem stem6"></div>

  <div class="flower flower1"></div>
  <div class="flower flower2"></div>
  <div class="flower flower3"></div>
  <div class="flower flower4"></div>
  <div class="flower flower5"></div>
  <div class="flower flower6"></div>

</body>
</html>
