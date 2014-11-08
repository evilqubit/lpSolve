<?php
/**
 * This is the "base controller class". All other "real" controllers extend this class.
 */
class Controller
{
    public $db = null;

    /**
     * Whenever a controller is created, open a database connection too. The idea behind is to have ONE connection
     * that can be used by multiple models (there are frameworks that open one connection per model).
     */
    function __construct()
    {
        //$this->openDatabaseConnection();
    }

    private function openDatabaseConnection()
    {
        // start mysql db here
        //$this->db = '';
    }

    public function loadModel($model_name)
    {
        require 'core/models/' . strtolower($model_name) . '.php';
        return new $model_name($this->db);
    }
}
