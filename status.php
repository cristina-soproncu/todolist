<?php
require "connect.php";
// Select all the todos, ordered by position:
$db = new PDORepository;
$queryList = $db->getTodoStatus();

if(isset($_GET['rebuild']) && $_GET['rebuild']=='triggers'){
    $db->rebuildAllTriggers();
}

if(isset($_GET['rebuild']) && $_GET['rebuild']=='procedures'){
    $db->rebuildAllProcedures();
}

?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>ToDo Status</title>
        <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
        <link rel="stylesheet" type="text/css" href="/css/material.min.css">
        <link rel="stylesheet" type="text/css" href="/css/styles.css">
    </head>
    <body class="status">
        <h1>ToDo Status</h1>
        <div id="main">
            <table class="mdl-data-table mdl-js-data-table">
                <?php $space = '&emsp;&emsp;&emsp;&emsp;'; ?>
                <thead>
                    <tr>
                        <th><?php echo $space . 'ID' . $space; ?></th>
                        <th><?php echo $space . 'Time' . $space; ?></th>
                        <th class="mdl-data-table__cell--non-numeric">
                            <?php echo $space . 'Action' . $space; ?>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($queryList as $row) {
                        $data = '';
                        $data .= '<tr>';
                        $data .= '<td>';
                        $data .= $space . $row['todo_id'] . $space;
                        $data .= '</td>';
                        $data .= '<td>';
                        $data .= $space . $row['time'] . $space;
                        $data .= '</td>';
                        $data .= '<td class="mdl-data-table__cell--non-numeric">';
                        $data .= $space . $row['method'] . $space;
                        $data .= '</td>';
                        $data .= '</tr>';

                        echo $data;
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.0/jquery-ui.min.js"></script>
        <script type="text/javascript" src="script.js"></script>
        <script type="text/javascript" src="/js/material.min.js"></script>
    </body>
</html>