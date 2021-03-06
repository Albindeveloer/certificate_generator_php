<?php

// Starting the session, necessary
// for using session variables

    if(!isset($_SESSION)) 
    { 
        session_start(); 
    } 


// Declaring and hoisting the variables
$Sname = "";
$email = "";

$usertype = "";
$dept_id = "";
$batch_id="";
$course_id="";

$errors = array();
$_SESSION['success'] = "";

// DBMS connection code -> hostname,
// username, password, database name
//$con = mysqli_connect('localhost', 'root', '','certi_db');

include ('connection.php');

// Registration code
if (isset($_POST['reg_user'])) {

	// Receiving the values entered and storing
	// in the variables
	// Data sanitization is done to prevent
	// SQL injections
	$Sname = mysqli_real_escape_string($conn, $_POST['username']);
	$email = mysqli_real_escape_string($conn, $_POST['email']);

    $usertype = mysqli_real_escape_string($conn, $_POST['UserType']);
    $dept_id = mysqli_real_escape_string($conn, $_POST['Dept_id']);
	$batch_id = mysqli_real_escape_string($conn, $_POST['Batch_id']);
	$course_id = mysqli_real_escape_string($conn, $_POST['Course_id']);

	$password_1 = mysqli_real_escape_string($conn, $_POST['password_1']);
	$password_2 = mysqli_real_escape_string($conn, $_POST['password_2']);

	// Ensuring that the user has not left any input field blank
	// error messages will be displayed for every blank input
	if (empty($Sname)) { array_push($errors, "Username is required"); }
	if (empty($email)) { array_push($errors, "Email is required"); }
    if (empty($usertype)) { array_push($errors, "usertype is required"); }
    if (empty($dept_id)) { array_push($errors, "dept_id is required"); }
	// if (empty($batch_id)) { array_push($errors, "batch_id is required"); }
	// if (empty($course_id)) { array_push($errors, "course_id is required"); }
	if (empty($password_1)) { array_push($errors, "Password is required"); }

	if ($password_1 != $password_2) {
		array_push($errors, "The two passwords do not match");
		// Checking if the passwords match
	}

	$sql = "SELECT * FROM staff WHERE batch_id='$batch_id' AND course_id='$course_id' LIMIT 1";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
		array_push($errors, "the user already exists");
    }



	// If the form is error free, then register the user
	if (count($errors) == 0) {
		
		// Password encryption to increase data security
		$password = md5($password_1);
		
		$file_name=$_FILES['profilepic']['name'];                            //array ayittan varune
		$temp_path=$_FILES['profilepic']['tmp_name'];

		$new_file_name=time().$file_name;                                  //timevach name koduthu

		move_uploaded_file($temp_path,"./uploads/".$new_file_name);                    //temppath,evidekano

    if($usertype=='HOD'){
		$query = "INSERT INTO staff (Sname,email,password,usertype,dept_id,batch_id,course_id,avatar)
				VALUES('$Sname', '$email', '$password', '$usertype','$dept_id',NULL,NULL,'$new_file_name')";

                echo $query;
		
		$rs=mysqli_query($conn, $query);
	}else{

		$query = "INSERT INTO staff (Sname,email,password,usertype,dept_id,batch_id,course_id,avatar)
				VALUES('$Sname', '$email', '$password', '$usertype','$dept_id','$batch_id','$course_id','$new_file_name')";

                echo $query;
		
		$rs=mysqli_query($conn, $query);

		

	}
	// Storing username of the logged in user,
		// in the session variable
		$_SESSION['username'] = $Sname;
		$_SESSION['avatar']=$new_file_name;
		$_SESSION['usertype'] = $usertype;
		
		// Welcome message
		$_SESSION['success'] = "You have logged in";
		
		// Page on which the user will be
		// redirected after logging in
        if($rs){
            header('location: ./facultCheckLogin.php');
        }else{
            echo "ERROR: Could not able to execute $query. " . mysqli_error($conn);
        }

		// Inserting data into table
				
	}
	
    
}

// User login
if (isset($_POST['login_user'])) {
	
	// Data sanitization to prevent SQL injection
	$Sname = mysqli_real_escape_string($conn, $_POST['username']);
	$password = mysqli_real_escape_string($conn, $_POST['password']);

	// Error message if the input field is left blank
	if (empty($Sname)) {
		array_push($errors, "Username is required");
	}
	if (empty($password)) {
		array_push($errors, "Password is required");
	}

	// Checking for the errors
	if (count($errors) == 0) {
		
		// Password matching
		$password = md5($password);
		
		$query = "SELECT * FROM staff WHERE Sname=
				'$Sname' AND password='$password'";
		$results = mysqli_query($conn, $query);

		// $results = 1 means that one user with the
		// entered username exists
		if (mysqli_num_rows($results) == 1) {

			$row = mysqli_fetch_row($results);
			print_r($row);
			$avatar=$row[8];
			$userType=$row[3];
			$batch_id=$row[5];
			$course_id=$row[6];
			$dept_id=$row[4];
			// Storing username in session variable
			$_SESSION['username'] = $Sname;
			$_SESSION['avatar']=$avatar;
			$_SESSION['usertype']=$userType;
			$_SESSION['batch_id']=$batch_id;
			$_SESSION['course_id']=$course_id;
			$_SESSION['dept_id']=$dept_id;
			
			// Welcome message
			$_SESSION['success'] = "You have logged in!";
			
			// Page on which the user is sent
			// to after logging in
			header('location: ./facultCheckLogin.php');
		}
		else {
			
			// If the username and password doesn't match
			array_push($errors, "Username or password incorrect");
		}
	}
}

?>
