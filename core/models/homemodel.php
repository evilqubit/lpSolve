<?php
class HomeModel
{
  function __construct($db) {
    try {
        $this->db = $db;
    } catch (PDOException $e) {
        exit('Database connection could not be established.');
    }
  }

  public function index(){
    include ('classes/scheduler.php');
    $scheduler = new Scheduler();
    return $scheduler;
    
    // $sql = 'SELECT * FROM about';
    // $query = $this->db->prepare($sql);
    // $query->execute();
    
    // return ( $query->rowCount() > 1 ) ? $query->fetchAll() : $query->fetch();
  }
    
  public function test($args=array()){
  }
}