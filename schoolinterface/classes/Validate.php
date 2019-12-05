<?php

/**
 * 11/12/2019
 * This is the validation file for the login menu
 *
 * @authors Maria Gallardo,Evan wheeler, Sukhveer Jawandha, Guangpeng Wu
 * @copyright 2019
 */

/**
 * This function returns true if the email and password match or false
 * if they do not
 * @return bool true or false
 */
function validUser()
{
    global $f3;

    //retrieve the post data
    $email = $f3->get('email');
    $password = $f3->get('password');

    //compare data, still need to work on password
    if ($email == 'admin@email.com' && $password == "123456789") {
        return true;
    }
    $f3->set("errors['login']", "Email/password combination does not match our records");
    return false;
}

/**
 * This function returns true if there is a content in the file or false if
 * the file is empty
 * @return bool true or false
 */
function validForm()
{
    global $f3;

    //retrieve the form name
    $form = $f3->get('file');

    if (!empty($form)) {
        return true;
    }

//    print_r("Please select a file");
    $f3->set("errors['upload']", "Please upload a file");
    return false;
}


/**
 *
 * This class checks if the input class is a valid input type or not
 * @return bool returns false or true
 */
function validClass()
{
    global $f3;

    //retrieve the form name
    $class = $f3->get('class');

    if (!empty($class)) {

        if (!is_numeric($class)) {
//            print_r("Your class has to be a numeric type");
            $f3->set("errors['upload']", "Class has to be a numeric type");
            return false;
        }
        return true;
    }
//    print_r("You forgot to enter a class");
    $f3->set("errors['upload']", "You forgot to enter a class");
    return false;
}

/**
 *
 * This class checks if the event name  is a valid input type or not
 * @return bool returns false or true
 */
function validEventName()
{
    global $f3;

    //retrieve the event name
    $event_name = $f3->get('event_name');

    if (empty($event_name)) {
//        print_r("You forgot to enter a class");
        $f3->set("errors['upload']", "You forgot to enter an Event Name");
        return false;
    }
//
    return true;
}

/**
 *
 * This class checks if the event name  is a valid input type or not
 * @return bool returns false or true
 */
function validDate()
{
    global $f3;

    //retrieve the event name
    $event_Date = $f3->get('event_date');

    if (empty($event_Date)) {
//        print_r("You forgot to enter a class");
        $f3->set("errors['upload']", "You forgot to enter event date.");
        return false;
    }
//
    return true;
}


//function validFile()
//{
//    global $f3;
//    if (isset($_POST['uploadform'])) {
//
//        $name = $_FILES['file']['name'];
//        $type = $_FILES['file']['type'];
//        $data = file_get_contents($_FILES['file']['tmp_name']);
//        if ($type != "application/pdf" &&
//            $type != "application/vnd.openxmlformats-officedocument.wordprocessingml.document" &&
//            $type != "application/msword") {
//            $f3->set("errors['upload']", "Only PDF, WordDoc/Docs file type are allowed ");
//            return false;
//        }
//        if (empty($file)) {
//            $f3->set("errors['upload']", "Please upload a file.");
//            return false;
//        }
//        return true;
//    }
//

//}

