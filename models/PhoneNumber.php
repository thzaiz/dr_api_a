<?php

class PhoneNumber{
    private $conn;

    public $UserID;
    public $LastName;
    public $FirstName;
    public $dob;
    public $ContactData;
    
    public function __construct($db) {
      $this->conn = $db;
    }

    public function read($num) {
            $nums = preg_replace("/[^[0-9,-]*$/", "", $num);
            $query = 'SELECT contactresources_to_users.UserID, users.LastName, users.FirstName, DATE_FORMAT(users.usr_dob, "%m-%d-%Y") AS dob, contactresources.ContactData 
                  FROM contactresources 
                  INNER JOIN contactresources_to_users ON contactresources.ContactResourceID = contactresources_to_users.ContactResourceID 
                  INNER JOIN users ON contactresources_to_users.UserID = users.UserID 
                  WHERE REPLACE(ContactData, "-", "") IN ('.$nums.') ORDER BY ContactData ASC';

      $stmt = $this->conn->prepare($query);
      $stmt->execute();
      return $stmt;

    }
  }
