<?php

class PDORepository {

    const USERNAME = "root";
    const PASSWORD = "";
    const HOST = "localhost";
    const DB = "todolist";

    private function getConnection() {
        $username = self::USERNAME;
        $password = self::PASSWORD;
        $host = self::HOST;
        $db = self::DB;
        $connection = new PDO("mysql:dbname=$db;host=$host", $username, $password);

        return $connection;
    }

    protected function queryList($sql, $args) {
        $connection = $this->getConnection();
        $stmt = $connection->prepare($sql);
        $stmt->execute($args);
        return $stmt;
    }

//    protected function checkProcedure($procName) {
//        $q = $this->queryList("select * from sys.objects", array());
//        $response = $q->fetchAll();
//        var_dump($response);
//        echo 'fdgcbnm,';
//    }

    //Procedure methods
    public function rebuildAllProcedures() {
        $this->deleteProcedure();
        $this->insertProcedure();
        $this->updateProcedure();
    }

    public function deleteProcedure() {
        $this->queryList("DROP PROCEDURE IF EXISTS deleteToDo", array());
        $this->queryList("CREATE PROCEDURE todolist.deleteToDo( IN itemId int )
          BEGIN 
            DELETE FROM list_todo WHERE id=itemId;
          END;", array());
    }

    public function insertProcedure() {
        $this->queryList("DROP PROCEDURE IF EXISTS insertToDo", array());
        $this->queryList("CREATE PROCEDURE todolist.insertToDo(IN strText varchar(100),IN position int)
          BEGIN 
            INSERT INTO list_todo (text,position) 
            VALUES (strText,position); 
          END;", array());
    }

    public function updateProcedure() {
        $this->queryList("DROP PROCEDURE IF EXISTS updateToDo", array());
        $this->queryList("CREATE PROCEDURE todolist.updateTodo( IN strText varchar(100),IN idItem int)
            BEGIN 
                UPDATE list_todo SET text=strText WHERE id=idItem; 
            END;", array());
    }
    
    /* Triggers */
    public function rebuildAllTriggers() {
        $this->deleteTrigger();
        $this->insertTrigger();
        $this->updateTrigger();
    }
    
    public function deleteTrigger(){
        $this->queryList("DROP TRIGGER on_todo_delete");
        $this->queryList("CREATE TRIGGER todolist.on_todo_delete AFTER DELETE ON todolist.list_todo FOR EACH ROW
        BEGIN
            INSERT INTO todo_status
            SET method = 'delete',
             todo_id = OLD.id,
            time = NOW();
        END;");
    }
    
    public function insertTrigger(){
        $this->queryList("DROP TRIGGER on_todo_insert");
        $this->queryList("CREATE TRIGGER todolist.on_todo_insert AFTER INSERT ON todolist.list_todo FOR EACH ROW
        BEGIN
            INSERT INTO todo_status
            SET method = 'insert',
            todo_id = NEW.id,
            time = NOW();
        END;");
    }
    
    public function updateTrigger(){
        $this->queryList("DROP TRIGGER on_todo_update");
        $this->queryList("CREATE TRIGGER todolist.on_todo_update BEFORE UPDATE ON todolist.list_todo FOR EACH ROW
        BEGIN
            INSERT INTO todo_status
            SET method = 'update',
            todo_id = OLD.id,
            time = NOW();
        END;");
    }

    /* ToDo Methods */

    public function getNextPosition() {
        //Use procedure
        $q = $this->queryList('CALL nextPosition()', array());
        $response = $q->fetch();

        return $response[0];
    }

    public function getPosition() {
        //Use procedure
        $q = $this->queryList('CALL getPosition()', array());
        $response = $q->fetch();

        return $response[0];
    }

    public function getTodoList() {
        //Use procedure
        $q = $this->queryList('SELECT * FROM list_todo ORDER BY position ASC', array());
        $response = $q->fetchAll();

        return $response;
    }

    public function inserTodo($text, $position) {
        $this->insertProcedure();
        //Use procedure
        $this->queryList('CALL insertToDo("' . $text . '", ' . $position . ')', array());
    }

    public function getLastInsertedID() {
        $pdo = $this->getConnection();

        return $pdo->lastInsertId();
    }

    public function deleteToDo($id) {
        $this->deleteProcedure();
        //Use procedure
        $this->queryList('CALL deleteToDo(' . $id . ')', array());
    }

    public function updateTodo($text, $id) {
        $this->updateProcedure();
        //Use procedure
        $this->queryList('CALL updateTodo("' . $text . '", ' . $id . ')', array());
    }

    public function reorder($positionsStr) {
        $this->queryList('UPDATE list_todo SET position = CASE id ' . join($positionsStr) . ' ELSE position END', array());
    }

    public function getTodoStatus() {
        $q = $this->queryList('SELECT * FROM todo_status ORDER BY time DESC', array());
        $result = $q->fetchAll();

        return $result;
    }

}

?>