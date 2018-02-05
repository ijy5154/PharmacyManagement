<?php
    class DB extends PDO    {
        private $host = 'localhost';
        private $user = 'pharmacy';
        private $password = 'qoWofk00';
        private $options = array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        );

        function __construct($database = 'pharmacy') {
            parent::__construct("mysql:host=$this->host;port=3306;dbname=$database;charset=utf8", $this->user, $this->password, $this->options);
        }
    }