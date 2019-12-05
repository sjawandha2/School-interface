<?php

//require "/home/aplusplu/config.php";
require "/home2/sjawandh/config.php";


/**
 * 11/16/2019
 * Class Database represents an instance of a database
 *
 * The class Databse creates a connection from remote site to a database,
 * if connection is successful the user can retrieve order information from the database.
 *
 * @authors Maria Gallardo, Sukhveer Jawandha, Evan Wheeler, Guangpeng Wu
 * @copyright 2019
 */
class Database
{
    private $_dbh;

    /**
     * Database constructor with no parameters, runs the connect method when instantiated
     */
    public function __construct()
    {
        $this->connect();
    }

    /**
     * This create a connection to database and returns an error if connection was not successful
     * @return PDO returns the connection
     */
    function connect()
    {
        try {

            //instantiate a db object
            $this->_dbh = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
            return $this->_dbh;

        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    /**
     * This function retrieves all forms from the database
     * @return mixed, all attributes from each form
     */
    function getAllForms()
    {
        $sql = "SELECT * FROM forms";

        //prepare, execute statements
        $statement = $this->_dbh->prepare($sql);
        $statement->execute();

        //extract data
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    /**
     * This function submits a form to the database
     * @param $file the permission slips' file
     * @param $event_name the name of the event
     * @param $event_date the date that the event will take place
     * @param $class the numeric value for the class number
     */
    function submitNewForm($file, $event_name, $event_date, $class)
    {
//        $name = $_FILES['file']['name'];
//        $type = $_FILES['file']['type'];
        $file = file_get_contents($_FILES['file']['tmp_name']);

        $sql = "INSERT INTO forms(form_id,event_name,file,event_date,class) VALUES 
                (form_id,:event_name,:file,:event_date,:class)";

        //2. Prepare the statement
        $statement = $this->_dbh->prepare($sql);

        //3. Bind parameters
        $statement->bindParam(':file', $file, PDO::PARAM_STR);
        $statement->bindParam(':class', $class, PDO::PARAM_STR);
        $statement->bindParam(':event_name', $event_name, PDO::PARAM_STR);
        $statement->bindParam(':event_date', $event_date, PDO::PARAM_STR);

        //4. execute the statement
        $statement->execute();
    }

    /**
     *This function returns all students who have confirmed a form
     * @param $form_id uses the id to retrieve information from a specific form
     * @return mixed returns all students who have confirmed a form
     */
    function getFormDetails($form_id)
    {
        $sql = "SELECT * FROM confirmed_forms,forms,students 
                WHERE forms.form_id = :form_id AND confirmed_forms.form_id = :form_id
                AND students.student_id = confirmed_forms.student_id";

        $statement = $this->_dbh->prepare($sql);

        //2.prepare statement
        $statement->bindParam(':form_id', $form_id, PDO::PARAM_STR);

        $statement->execute();

        $row = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $row;

    }

    function submitStudentForm($form_id, $student_id, $fname, $lname, $notes, $pFName, $pLName, $signature)
    {
        //check if student is new student, create account
        if (!$this->getStudentInfo($student_id)) {
            $this->createNewStudent($student_id, $fname, $lname);
        }

//add the confirmed form to the database records
        $sql = "INSERT INTO confirmed_forms VALUES (:form_id,:student_id,:notes,:pFName,:pLName,:signature)";

        $statement = $this->_dbh->prepare($sql);

        $statement->bindParam(':form_id', $form_id, PDO::PARAM_INT);
        $statement->bindParam(':student_id', $student_id, PDO::PARAM_INT);
        $statement->bindParam(':notes', $notes, PDO::PARAM_STR);
        $statement->bindParam(':pFName', $pFName, PDO::PARAM_STR);
        $statement->bindParam(':pLName', $pLName, PDO::PARAM_STR);
        $statement->bindParam(':signature', $signature, PDO::PARAM_STR);


        $statement->execute();

    }

    /**
     * This function returns the information for a student found in the database
     * @param $student_id the student's id
     * @return mixed returns the row containing all the student's information
     */
    function getStudentInfo($student_id)
    {
        $sql = "SELECT * FROM students WHERE student_id = :student_id";

        $statement = $this->_dbh->prepare($sql);

        //2.prepare statement
        $statement->bindParam(':student_id', $student_id, PDO::PARAM_STR);

        $statement->execute();

        $row = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $row;
    }

    function getEachStudentInfo($form_id, $student_id)
    {

        $sql = "SELECT * FROM students ,confirmed_forms , forms WHERE forms.form_id = :form_id && students.student_id = :student_id && confirmed_forms.student_id = :student_id";

        $statement = $this->_dbh->prepare($sql);

        //2.prepare statement
        $statement->bindParam(':student_id', $student_id, PDO::PARAM_STR);
        $statement->bindParam(':form_id', $form_id, PDO::PARAM_INT);


        $statement->execute();

        $row = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $row;
    }

    /**
     * This function creates a new student in the database
     * @param $student_id the student id
     * @param $fname the student's first name
     * @param $lname the student's last name
     */
    function createNewStudent($student_id, $fname, $lname)
    {
        $sql = "INSERT INTO students VALUES(:student_id,:fname,:lname)";

        $statement = $this->_dbh->prepare($sql);

        $statement->bindParam(':student_id', $student_id, PDO::PARAM_STR);
        $statement->bindParam(':fname', $fname, PDO::PARAM_STR);
        $statement->bindParam(':lname', $lname, PDO::PARAM_STR);

        $statement->execute();
    }

    function getEventName($form_id)
    {
        $sql = "SELECT event_name FROM forms 
                WHERE form_id = :form_id";


        $statement = $this->_dbh->prepare($sql);

        //2.prepare statement
        $statement->bindParam(':form_id', $form_id, PDO::PARAM_STR);

        $statement->execute();

        $row = $statement->fetch(PDO::FETCH_ASSOC);
        return $row;

    }

    //to be deleted for testing purposes only.....
    function submit($fname)
    {
        $sql = "INSERT INTO names VALUES(:fname)";

        $statement = $this->_dbh->prepare($sql);

        $statement->bindParam(':fname', $fname, PDO::PARAM_STR);

        $statement->execute();
    }


}