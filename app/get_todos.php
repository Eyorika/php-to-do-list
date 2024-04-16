<?php
require '../db_conn.php';
// author : Eyob Ayalew
// Fetch todos from the database
$todos = $conn->query("SELECT * FROM todos ORDER BY id DESC");

if ($todos->rowCount() > 0) {

    $output = '';
    while ($todo = $todos->fetch(PDO::FETCH_ASSOC)) {
        $output .= '<div class="todo-item">';
        $output .= '<span id="' . $todo['id'] . '" class="remove-to-do">x</span>';
        if ($todo['checked']) {
            $output .= '<input type="checkbox" class="check-box" data-todo-id="' . $todo['id'] . '" checked />';
            $output .= '<h2 class="checked">' . $todo['title'] . '</h2>';
        } else {
            $output .= '<input type="checkbox" data-todo-id="' . $todo['id'] . '" class="check-box" />';
            $output .= '<h2 contenteditable="true" class="editable">' . $todo['title'] . '</h2>';
        }
        $output .= '<br>';
        $output .= '<small>created: ' . $todo['date_time'] . '</small>';
        $output .= '</div>';
    }

    echo $output;
} else {
    
    echo '<div class="todo-item empty">';
    echo '<div class="empty-content">';
    echo '<img src="https://i.pinimg.com/564x/1a/db/f8/1adbf889eb95ad184e7f797d40c1cd64.jpg" width="100%"/>';
    echo '<p>No tasks found!</p>';
    echo '<button class="add-task-button">Add Task</button>';
    echo '</div>';
    echo '</div>';
}


?>
