<?php

//Security Measure. Prevents a user from going to the URL
//and running this file. They need to check if we do
//have submit acutally clicked
if (isset($_POST['submit'])) {

  include_once 'dbh.inc.php';

 //This allows a user to write something and send it to the dababase and we
 //Wdon't want someone to insert code into our database so
 //we use the mysqli real escape string to make sure that they can't
 //this converts it to text
  $first = mysqli_real_escape_string($conn, $_POST['first']);
  $last = mysqli_real_escape_string($conn, $_POST['last']);
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $uid = mysqli_real_escape_string($conn, $_POST['uid']);
  $pwd = mysqli_real_escape_string($conn, $_POST['pwd']);

  //Error handlers
  //dont allow users to sign up if they dont fill out the form correctly

  //Check for empty fields
  if (empty($first) || empty($last) || empty($email) ||
      empty($uid) || empty($pwd) ) {
        header("Location: ../signup.php?signup=empty");
        exit();//closes off and stops this script from running
  }
  else {
    //Check if input characters are valid
    //preg_match checks if we have certain characters inside a string
    if (!preg_match("/^[a-zA-Z]*$/", $first) || !preg_match("/^[a-zA-Z]*$/", $last)) {
      header("Location: ../signup.php?signup=invalid");
      exit();//closes off and stops this script from running
    }
    else {
      //Check if email is valid
      if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: ../signup.php?signup=email");
        exit();//closes off and stops this script from running
      }
      else {
        //check if username already is in the DATABASE
        $sql = "SELECT * FROM users WHERE user_uid='$uid'";
        $result = mysqli_query($conn, $sql);//Connection to database and actual statement to run inside database
        $resultCheck = mysqli_num_rows($result);//query and check if we have any rows as a result, a row is returned each time we get a result
                                                //from the database
        if ($resultCheck > 0) {
          header("Location: ../signup.php?signup=userTaken");
          exit();//closes off and stops this script from running
        }
        else {
          //Hashing the password
          $hashedPwd = password_hash($pwd, PASSWORD_DEFAULT);
          //Insert the user into the database
          $sql = "INSERT INTO users (user_first, user_last,
            user_email, user_uid, user_pwd) VALUES ('$first', '$last', '$email', '$uid', '$hashedPwd');";
          mysqli_query($conn, $sql);
          header("Location: ../signup.php?signup=success");
          exit();//closes off and stops this script from running
        }
      }
    }
  }

}
//went to address, send back to the sign up page
else {
  header("Location: ../signup.php");
  exit();//closes off and stops this script from running
}
