<?php

class StaffPhoneNumber{
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
            $query = 'SELECT contactresources_to_users.UserID, users.LastName, users.FirstName, DATE_FORMAT(users.usr_dob, "%m-%d-%Y") AS dob,
                 (SELECT ContactData FROM contactresources INNER JOIN contactresources_to_users ON contactresources.ContactResourceID = contactresources_to_users.ContactResourceID WHERE contactresources_to_users.UserID = users.UserID AND ResourceTypeID = 7 AND CRStatus = 1 LIMIT 1) as cell_phone,
                 (SELECT ContactData FROM contactresources INNER JOIN contactresources_to_users ON contactresources.ContactResourceID = contactresources_to_users.ContactResourceID WHERE contactresources_to_users.UserID = users.UserID AND ResourceTypeID = 5 AND CRStatus = 1 LIMIT 1) as home_phone,
                 (SELECT ContactData FROM contactresources INNER JOIN contactresources_to_users ON contactresources.ContactResourceID = contactresources_to_users.ContactResourceID WHERE contactresources_to_users.UserID = users.UserID AND ResourceTypeID = 8 AND CRStatus = 1 LIMIT 1) as work_phone,
                 (SELECT ContactData FROM contactresources INNER JOIN contactresources_to_users ON contactresources.ContactResourceID = contactresources_to_users.ContactResourceID WHERE contactresources_to_users.UserID = users.UserID AND ResourceTypeID = 141 AND CRStatus = 1 LIMIT 1) as emergency_phone,
                 (SELECT ContactData FROM contactresources INNER JOIN contactresources_to_users ON contactresources.ContactResourceID = contactresources_to_users.ContactResourceID WHERE contactresources_to_users.UserID = users.UserID AND ResourceTypeID = 9 AND CRStatus = 1 LIMIT 1) as email,
                 (SELECT CONCAT (AddrLine1, " ", AddrLine2, " ", AddrCity, " ", AddrStateID, " ", REPLACE(replace(AddrZip, "-", ""), "_", ""), " ", AddrCountryID) AS address FROM addresses INNER JOIN address_to_users ON addresses.Addr_ID = address_to_users.a2u_address_id INNER JOIN users ON users.UserID = address_to_users.a2u_usr_id WHERE a2u_usr_id = contactresources_to_users.UserID ORDER BY Addr_ID DESC LIMIT 1) AS res_address, 
                  users.usr_pay_rate,
                 (SELECT IFNULL((SELECT from_unixtime(tc_timestamp) from time_clock_bkp_01_Sep_2016 where tc_user_id = contactresources_to_users.UserID LIMIT 1),(SELECT from_unixtime(tc_timestamp) from time_clock where tc_user_id = contactresources_to_users.UserID LIMIT 1))) AS first_timepunch,
                 (SELECT IFNULL((SELECT from_unixtime(tc_timestamp) from time_clock where tc_user_id = contactresources_to_users.UserID ORDER BY tc_timestamp DESC LIMIT 1),(SELECT from_unixtime(tc_timestamp) from time_clock_bkp_01_Sep_2016 where tc_user_id = contactresources_to_users.UserID ORDER BY tc_timestamp DESC LIMIT 1))) as last_timepunch,
                 (SELECT from_unixtime(li_log_dt_tm) from log_info where li_user_id = contactresources_to_users.UserID ORDER BY li_id DESC LIMIT 1) as last_login,
                  users.Enabled as status
                  FROM contactresources 
                  INNER JOIN contactresources_to_users ON contactresources.ContactResourceID = contactresources_to_users.ContactResourceID 
                  INNER JOIN users ON contactresources_to_users.UserID = users.UserID
                  LEFT OUTER JOIN users_patient_bio ON users.UserID = users_patient_bio.pat_user_id
                  WHERE users_patient_bio.pat_id IS null
                  AND REPLACE(ContactData, "-", "") IN ('.$nums.')
                  AND contactresources.CRStatus = 1 
                  ORDER BY ContactData ASC';

      $stmt = $this->conn->prepare($query);
      $stmt->execute();
      return $stmt;

    }
  }