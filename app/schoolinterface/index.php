<?php
/**
 * 11/12/2019
 * This is the controller file for the school administrator interface
 *
 * @authors Maria Gallardo,Evan wheeler, Sukhveer Jawandha, Guangpeng Wu
 * @copyright 2019
 */

use Mpdf\Mpdf;

require "vendor/autoload.php";
require_once __DIR__ . "/vendor/autoload.php";

$f3 = Base::instance();
$f3->set("DEBUG", 3);
$f3->set("title", "Dashboard");
require_once('classes/Validate.php');
require('classes/database.php');

session_start();

$db = new Database();
$mpdf = new Mpdf();


//add the default route
$f3->route('GET|POST /', function ($f3) {

    //check if user made attempt to login
    if (!empty($_POST)) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        //add values to the hive
        $f3->set('email', $email);
        $f3->set('password', $password);

        if (validUser()) {
            $f3->reroute('/upload');
        }
    }

    $view = new Template();
    echo $view->render("views/login.html");
});


$f3->route("GET|POST /upload", function ($f3) {
    global $db;
    if (!empty($_POST)) {
        $class = $_POST['class'];
        $file = $_POST['file'];
        $event_name = $_POST['event_name'];
        $event_date = $_POST['event_date'];

        $f3->set('file', $file);
        $f3->set('class', $class);
        $f3->set('event_name', $event_name);
        $f3->set('event_date', $event_date);

        $_SESSION['class'] = $class;
        $_SESSION['file'] = $file;
        $_SESSION['event_name'] = $event_name;
        $_SESSION['event_date'] = $event_date;

        if (!empty($file) && validForm() && validClass() && validEventName() && validDate()) {
            $db->submitNewForm($file, $event_name, $event_date, $class);
            $f3->reroute('/confirmation');
        }
    }

    $view = new Template();
    echo $view->render("views/upload.html");
});

$f3->route("GET|POST /forms", function ($f3) {
    global $db;

    if (!empty($_POST)) {
        $form_id = $_POST['form_id'];
        $students = $db->getFormDetails($form_id);
        $f3->set('students', $students);
        $_SESSION['students'] = $students;
        $_SESSION['form_id'] = $form_id;

        $f3->reroute('/details');

    }


    //retrieve all the forms from the database
    $forms = $db->getAllForms();
    $f3->set('forms', $forms);

    $view = new Template();
    echo $view->render("views/all_forms.html");
});

$f3->route("GET|POST /confirmation", function () {
    $view = new Template();
    echo $view->render("views/confirm.html");
});

//Define a route that displays form detail
$f3->route('GET|POST /details', function ($f3) {
    global $db;
    global $mpdf;
    $students = $_SESSION['students'];
    $form_id =$_SESSION['form_id'];
    $event_name= $db->getEventName($form_id);
    $_SESSION['event_name'] = $event_name['event_name'];
    $f3->set('students', $students);

print_r($students);

    if(!empty($_POST)) {
        //get the student info
//        getEachStudentInfo

        $student_id = $_POST['student_id'];
        $students = $db-> getStudentInfo($student_id);
        $_SESSION['student_id'] = $student_id;
        $data = "";
        $data .= "<h1>{$_SESSION['event_name']} Event Detail</h1>";

        //add data
        $data .= "<strong>Student First Name: </strong>" . "<strong>Student Last Name: </strong>" . "<br />";
        $data .= "<strong>Student ID: </strong>" . $student_id. "<strong>Class: </strong>" . "<hr />";
        $data .= "<strong>Special Requests: </strong>" . "<br />";
        $data .= "<strong>Parent First Name: </strong>" . "<strong>Parent Last Name: </strong>" . "<br />";
        $data .= "<strong>Signature: </strong>" . "<br />";


//write PDF
        $mpdf->WriteHTML($data);

//output to browser
    $mpdf->Output('Event.pdf','D');
    }

// print_r($students);
    $template = new Template();
    echo $template->render('views/form_detail.html');
});

$f3->route("GET|POST /studentSubmit", function () {
    global $db;

    if (!empty($_POST)) {
        $form_id = $_POST['form_id'];
        $student_id = $_POST['student_id'];
        $fname = $_POST['fname'];
        $lname = $_POST['lname'];
        $notes = $_POST['notes'];
        $pFName = $_POST['pFName'];
        $pLName = $_POST['pLName'];
        // if(isset($_POST['tester']))
        // {
        // $target = "/images/".basename($_FILES['signature']['name']).
        // $signature = $_FILES['signature']['name'];
        // }

        $db->submitStudentForm($form_id, $student_id, $fname, $lname, $notes, $pFName, $pLName, $signature);

        // if(move_uploaded_file($_FILES['tmp_name']['name'],$target))
        // {
        //     echo "Success";
        // }
        // {
        //  print_r($signature."Heloo");
        //     echo "Not Successful";

            
        // }
    }
    $template = new Template();
    echo $template->render('views/tester.html');
});

//to be delete it, for testing purposes only
$f3->route("GET|POST /submit", function () {
    global $db;

    if (!empty($_POST)) {
        $fname = $_POST['fname'];

        $db->submit($fname);
    }
    $template = new Template();
    echo $template->render('views/tester.html');
});

$f3->run();