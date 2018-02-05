<?php
require "connect.php";
require "todo.class.php";
// Select all the todos, ordered by position:
$db = new PDORepository;
$queryList = $db->getTodoList();
$todos = array();
// Filling the $todos array with new ToDo objects:
foreach ($queryList as $row) {
    $todos[] = new ToDo($row);
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>ToDo List</title>
        <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
        <link rel="stylesheet" type="text/css" href="/css/material.min.css">
        <link rel="stylesheet" type="text/css" href="/css/styles.css">
    </head>
    <body background="bgimage.png">
        <div class="mdl-grid">
            <h1 class="title">Today Priority List</h1>
            <h3 class="date">
                <span class="mdl-badge" data-badge="<?php echo count($todos); ?>">
                    <?php echo date('d M Y'); ?>
                </span>
            </h3>
            <div id="main">
                <ul class="mdl-list todoList">
                    <?php
                    foreach ($todos as $item) {
                        echo $item;
                    }
                    ?>
                </ul>
                <button id="addButton" class="mdl-button mdl-js-button mdl-button--fab mdl-js-ripple-effect mdl-button--colored">
                    <i class="material-icons">add</i>
                </button>
                <a class="status-link" href="/status.php">
                    <i class="material-icons">settings</i>
                </a>
            </div>
        </div>
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.0/jquery-ui.min.js"></script>
        <script type="text/javascript" src="script.js"></script>
        <script type="text/javascript" src="/js/material.min.js"></script>
    </body>
</html>
