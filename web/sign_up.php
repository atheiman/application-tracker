<?php
session_start();
if (isset($_SESSION['email'])) {header('Location: home.php');}
$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
  $_POST['email'] = test_input($_POST['email']);
  $_POST['password'] = test_input($_POST['password']);
  $_POST['confirm_password'] = test_input($_POST['confirm_password']);
  $_POST['firstname'] = test_input($_POST['firstname']);
  $_POST['lastname'] = test_input($_POST['lastname']);
  $_POST['city'] = test_input($_POST['city']);
  $_POST['state'] = test_input($_POST['state']);
  $_POST['phone'] = test_input($_POST['phone']);

  include 'resources/db_connect.php';
  // Check if email already exists
  $sql = "select applicant_email from applicants where applicant_email = '" . $_POST['email'] . "'";
  $result = mysqli_query($con,$sql);
  if ($result->num_rows != 0 ) {
    echo "<script>
    alert('There is already an account associated with ".$_POST['email'].", redirecting you to login page.');
    window.location.assign('login.php?email=".$_POST['email']."');
    </script>";
  }
  mysqli_free_result($result);
  // Add user to applicants table
  $sql = "insert into applicants ( applicant_email , password , firstname , lastname , city , state , phone )
  values
  ( '".$_POST['email']."' , '".$_POST['password']."' , '".$_POST['firstname']."' , '".$_POST['lastname']."' , '".$_POST['city']."' , '".$_POST['state']."' , '".$_POST['phone']."')";
  $result = mysqli_query($con,$sql);
  if (mysqli_connect_errno()) {die("Failed to connect to MySQL: " . mysqli_connect_error());}
  // Set Session vars
  $_SESSION['email'] = $_POST['email'];
  $_SESSION['firstname'] = $_POST['firstname'];
  echo "<script>window.location.assign('home.php');</script>";
}

function test_input($data) {
   $data = trim($data);
   $data = stripslashes($data);
   $data = htmlspecialchars($data);
   return $data;
}
?>

<!doctype html>
<head>
<title>App-ly</title>
</head>
<body>
<div id='page_title'>
Welcome to App-ly
</div>
<div id='content'>
Sign up<br>
<form method='post' name='login_form' action='<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>' onsubmit="return validateForm()" >
Email: <input id='email_input' type='email' name='email' size='20' required><br>
Password: <input id='password_input' type='password' name='password' required><br>
Confirm Password: <input id='confirm_pass_input' type='password' name='confirm_password' required><br>
First name: <input id='firstname_input' type='text' name='firstname' required><br>
Last name: <input id='lastname_input' type='text' name='lastname' required><br>
City: <input id='city_input' type='text' name='city' required><br>
State: <input id='state_input' type='text' name='state' size='2' maxlength='2' required onchange='set_state();'><br>
Phone: 
<input id='phone1' type='number' name='phone1' size='3' max='999' min='0' required onchange='set_phone()' onkeyup='if (phone1.value.toString().length == 3) {phone2.focus();}'>-<input id='phone2' type='number' name='phone2' size='3' max='999' min='0' required onchange='set_phone()' onkeyup='if (phone2.value.toString().length == 3) {phone3.focus();}' onfocus='if (phone1.value.toString().length == 0) {phone1.focus();}'>-<input id='phone3' type='number' name='phone3' size='4' max='9999' min='0' required onchange='set_phone()' onfocus='if (phone2.value.toString().length == 0) {phone2.focus();}'>
<input id='phone' type='text' name='phone' size='20' hidden><br>
<input type='submit'><br>
</form>
<div id='error'>
<?php
if ($error != '') {echo "Error: " . $error;}
echo "<script>";
if ($error == 'incorrect password') {
  echo "
  document.forms['login_form']['email'].value = '" . $_POST['email'] . "';
  document.getElementById('password_input').focus();
  ";
} else {
  echo "
  document.getElementById('email_input').focus();
  ";
}
echo "</script>";
?>
</div>
<br>Already a member? <a href='login.php'>Login</a><br>
</div>
<script>
var phone1=document.getElementById('phone1');
var phone2=document.getElementById('phone2');
var phone3=document.getElementById('phone3');

function set_state() {
  var x=document.getElementById("state_input");
  x.value=x.value.toUpperCase();
}
function set_phone() {
  var phone1=document.forms['login_form']['phone1'].value.toString();
  var phone2=document.forms['login_form']['phone2'].value.toString();
  var phone3=document.forms['login_form']['phone3'].value.toString();
  var phone=document.forms['login_form']['phone'];
  phone.value=phone1+'-'+phone2+'-'+phone3;
}

function validateForm()
{
  var email=document.forms['login_form']['email'].value;
  var password=document.forms['login_form']['password'].value;
  var confirm_password=document.forms['login_form']['confirm_password'].value;
  var firstname=document.forms['login_form']['firstname'].value;
  var lastname=document.forms['login_form']['lastname'].value;
  var city=document.forms['login_form']['city'].value;
  var state=document.forms['login_form']['state'].value;
  state=state.toUpperCase();
  var phone=document.forms['login_form']['phone'].value;
  
  var error = '';
  if (password != confirm_password) {
    error = error.concat('\'Password\' and \'Confirm Password\' do not match.\n');
  }
  if (phone.length != 12) {
    error = error.concat('Phone format incorrect. Format should be XXX-XXX-XXXX\n');
  }
  if (state.length != 2) {
    error = error.concat('State should be a 2-digit code. Ex: \'Kansas\' is \'KS\'.\n');
  }
  if (error != '') {
    alert(error);
    return false;
  }
}
</script>
</body>
</html>
