<?php 
require 'db_conn.php';


session_start();

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];


if(isset($_POST['title'])) {
    $title = $_POST['title'];
    $priority = $_POST['priority'];
    $due_date = $_POST['due_date'];
    
    $stmt = $conn->prepare("INSERT INTO todos (user_id, title, priority, due_date) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $title, $priority, $due_date]);
    header("Location: index.php");
    exit();

    
}

if(isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM todos WHERE user_id = ? ORDER BY id DESC");
$stmt->execute([$user_id]);
$todos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>To-Do List</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEAg3QhqLMpG8r+Knujsl5+z8r4L2f8dtw3S5P9w5TOw6PvqFq5ETHyNxs2rD">

    <link rel="stylesheet" href="css/style.css">
<style>

        /* CSS */
        .custom-select {
        font-size: 16px;
        color: #495057; 
        padding: .375rem .75rem;
        background-color: #80bdff;
        border: 3px solid #ced4da;
        border-radius: 5px; 
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out; /* Transition effects */
    }

    .custom-select:focus {
        border-color: #80bdff; 
        outline: 0; 
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25); 
    }

    .spinner {
        display: block;
    width: 50px;
    height: 50px;
    border: 5px solid #ccc;
    border-top-color: #007bff;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}


</style>
</head>
<body>
    <div class="main-section">
   <div class="text-effect">
        <p class="animated-text">አምላኬ ታሪኬን ለዉጠዉ</p>
        <p class="animated-text">ጌታየ ታሪኬን ለዉጠዉ</p>
        <p class="animated-text">ሠዉ አርገኝና ሠዉ ይግረመዉ</p>
    </div>
    
 <!--<div class="div">
     <div >አምላኬ ታሪኬን ለዉጠዉ</div> 
  <span class="span-1">ጌታየ ታሪኬን ለዉጠዉ</span>
  <p class="p-1">ሠዉ አርገኝና ሠዉ ይግረመዉ</p>

</div>-->



    <div class="sign-out-section">
            <a href="index.php?logout=true">Sign Out</a>
     </div>
       
    <div class="add-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <form id="add-form" action="app/add.php" method="POST" autocomplete="off">
                    <div class="form-group">
                        <?php if(isset($_GET['mess']) && $_GET['mess'] == 'error'){ ?>
                            <input type="text" name="title" class="form-control is-invalid" placeholder="This field is required" required>
                            <div class="invalid-feedback">This field is required</div>
                        <?php }else{ ?>
                            <input type="text" name="title" class="form-control" placeholder="What do you need to do?" required>
                        <?php } ?>
                    </div>
                    <div class="form-group">
                    <select name="priority" class="form-control custom-select" required>
                      <option value="">Select priority</option>
                      <option value="low">Low</option>
                      <option value="medium">Medium</option>
                      <option value="high">High</option>
                 </select>
                    </div>

                    <div class="form-group">
                        <input type="date" name="due_date" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary">Add <span>&#43;</span></button>
                    <div class="feedback-message mt-2"></div>
                </form>
            </div>
        </div>
    </div>
</div>

       <?php 
          $todos = $conn->query("SELECT * FROM todos ORDER BY id DESC");
       ?>
       <div class="show-todo-section">
            <?php if($todos->rowCount() <= 0){ ?>
                <div class="todo-item empty">
                <div class="empty-content">
    <div class="spinner" id="spinner"></div>
    <p id="no-tasks-msg">No tasks found!</p>
    <button class="add-task-button">Add Task</button>
</div>



</div>

            <?php } ?>

            <?php while($todo = $todos->fetch(PDO::FETCH_ASSOC)) { ?>
    <div class="todo-item" data-priority="<?php echo $todo['priority']; ?>">
        <span id="<?php echo $todo['id']; ?>" class="remove-to-do">x</span>
        <?php if($todo['checked']){ ?> 
            <input type="checkbox" class="check-box" data-todo-id="<?php echo $todo['id']; ?>" checked />
            <h2 class="checked"><?php echo $todo['title'] ?></h2>
        <?php } else { ?>
            <input type="checkbox" data-todo-id="<?php echo $todo['id']; ?>" class="check-box" />
            <h2 contenteditable="true" class="editable"><?php echo $todo['title'] ?></h2>
        <?php } ?>
        <p>Priority: <?php echo $todo['priority']; ?></p> 
        <br>
        <small>created: <?php echo $todo['date_time'] ?></small> 
    </div>
<?php } ?>


       </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    $('.add-task-button').click(function() {
    $('#title').blur(); 
});

    $(document).ready(function() {

        $(document).on('click', '.remove-to-do', function() {
            const id = $(this).attr('id');
            
            $.post("app/remove.php", 
                {
                    id: id
                },
                (data) => {
                    if (data) {
                        $(this).parent().hide(600);
                    }
                }
            );
        });

        $(document).on('change', '.check-box', function() {
            const id = $(this).data('todo-id');
            
            $.post('app/check.php', 
                {
                    id: id
                },
                (data) => {
                    if (data !== 'error') {
                        const h2 = $(this).next();
                        if (data === '1') {
                            h2.removeClass('checked');
                        } else {
                            h2.addClass('checked');
                        }
                    }
                }
            );
        });

       

        function updateTodoList() {
            $.get("app/get_todos.php", function(data) {
                $(".show-todo-section").html(data); 
            });
        }

        updateTodoList();

        $('#add-form').submit(function() {
            var title = $('#title').val().trim();
            var priority = $('#priority').val();
            
            if (!title || !priority) {
                $('.feedback-message').text('Please fill out all required fields');
                return false; 
            }
        });

        $('.sign-out-button').click(function() {
            window.location.href = 'logout.php';
        });
    });


document.getElementById('spinner').style.display = 'block';
document.getElementById('no-tasks-msg').style.display = 'none'; 

fetchTodos();

function fetchTodos() {
    $.get("app/get_todos.php", function(data) {
        $(".show-todo-section").html(data); 

        document.getElementById('spinner').style.display = 'none';

        if (data.trim() === '') {
            document.getElementById('no-tasks-msg').style.display = 'block';
        } else {
            document.getElementById('no-tasks-msg').style.display = 'none';
        }
    });
}

$('#add-form').submit(function(event) {
        event.preventDefault(); 
        var priority = $('#priority').val();
        var formData = new FormData(this);
        formData.append('priority', priority);
        
        $(this).find(':input').prop('disabled', true);
        
        fetch('app/add.php', {
            method: 'POST',
            body: formData
        }).then(response => {
            console.log(response);

            updateTodoList();
            
            $(this).find(':input').val('');
            
            $(this).find(':input').prop('disabled', false);
        }).catch(error => {
            console.error('Error:', error);
            
            $(this).find(':input').prop('disabled', false);
        });
    });


 
// text effect


</script>


<script src="js/jscript.js"></script>

</body>
</html>