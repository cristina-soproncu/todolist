<?php

/* Defining the ToDo class */

class ToDo {
    /* An array that stores the todo item data: */

    private $data;

    /* The constructor */

    public function __construct($par) {
        if (is_array($par))
            $this->data = $par;
    }

    /*
      This is an in-build "magic" method that is automatically called
      by PHP when we output the ToDo objects with echo.
     */

    public function __toString() {
        $pos = isset($this->data['position']) ? $this->data['position'] : '';
        // The string we return is outputted by the echo statement
        return '<li id="todo-' . $this->data['id'] . '" class="mdl-list__item mdl-list__item--three-line todo">
        <span class="mdl-list__item-primary-content">
          <i class="material-icons mdl-list__item-avatar">person</i>
          <span>Task ' . $pos . '</span>
          <span class="mdl-list__item-text-body text">' . $this->data['text'] . '</span>
        </span>
        <span class="mdl-list__item-secondary-content">
          <a class="mdl-list__item-secondary-action edit" href="#">
            <i class="material-icons">edit</i>
          </a>
          <a class="mdl-list__item-secondary-action delete" href="#">
            <i class="material-icons">delete</i>
          </a>
        </span></li>';
    }

    /*
      The edit method takes the ToDo item id and the new text
      of the ToDo. Updates the database.
     */

    public static function edit($id, $text) {
        $text = self::esc($text);
        if (!$text)
            throw new Exception("Wrong update text!");

        $db = new PDORepository;
        $db->updateTodo($text, $id);
    }

    /*
      The delete method. Takes the id of the ToDo item
      and deletes it from the database.
     */

    public static function delete($id) {
        $db = new PDORepository;
        $db->deleteToDo($id);
    }

    /*
      The rearrange method is called when the ordering of
      the todos is changed. Takes an array parameter, which
      contains the ids of the todos in the new order.
     */

    public static function rearrange($key_value) {
        $strVals = array();
        foreach ($key_value as $k => $v) {
            $strVals[] = 'WHEN ' . (int) $v . ' THEN ' . ((int) $k + 1) . PHP_EOL;
        }

        if (!$strVals)
            throw new Exception("No data!");

        $db = new PDORepository;
        $db->reorder($strVals);
    }

    /*
      The createNew method takes only the text of the todo,
      writes to the databse and outputs the new todo back to
      the AJAX front-end.
     */

    public static function createNew($text) {
        $text = self::esc($text);
        if (!$text)
            throw new Exception("Wrong input data!");

        $db = new PDORepository;

        $position = $db->getNextPosition();

        if (!$position)
            $position = 1;

        $db->inserTodo($text, $position);

        // Creating a new ToDo and outputting it directly:
        echo (new ToDo(array(
    'id' => $db->getLastInsertedID(),
    'text' => $text
        )));

        exit;
    }

    /*
      A helper method to sanitize a string:
     */

    public static function esc($str) {
        if (ini_get('magic_quotes_gpc'))
            $str = stripslashes($str);

        return mysql_real_escape_string(strip_tags($str));
    }

}

// closing the class definition
?>