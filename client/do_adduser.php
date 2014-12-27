<?php

//This is required to get the international text strings dictionary
require_once 'internationalize.php';

//check for required fields
if ((!$_POST['firstname']) || (!$_POST['lastname']) || (!$_POST['username']) || (!$_POST['password']) || (!$_POST['authority'])) {
	header("Location: adduser.php");
	exit;
}

//check authority to be here
require_once 'authorization_check.php';

//All queries go through a translator. 
require_once 'DBTranslator.php';


//Checking for an existing user
$sql ="Select username FROM moss_users WHERE username ='$_POST[username]' ORDER BY username";
$result = transQuery($sql,0,0);
//adding a new user if there isn't one already
if (count($result) < 1){
	
	$first_name = $_POST['firstname'];
	$first_name = mysqli_real_escape_string($first_name);
	$last_name = $_POST['lastname'];
	$last_name = mysqli_real_escape_string($last_name);
	$user_name = $_POST['username'];
	$user_name = mysqli_real_escape_string($user_name);
	$pass_word = $_POST['password'];
	$pass_word = mysqli_real_escape_string($pass_word);
	$authority = $_POST['authority'];
	$authority = mysqli_real_escape_string($authority);
	
	$sql ="INSERT INTO moss_users(firstname, lastname, username, password, authority) VALUES ('$first_name', '$last_name', '$user_name', PASSWORD('$pass_word'), '$authority')";

$result = transQuery($sql,0,-1);

//get a good message for display upon success
if ($result){ 
$msg = "<p class=em2> $Congrats  $_POST[firstname].  $AddAnother  </p>";
	}
}
//Display the appropriate user authority to add depending on the user's authority
if (isAdmin()){
	$selection = "<select name=authority id=authority><option value=>".$SelectEllipsis."</option><option value=admin>".$Administrator."</option><option value=teacher>".$Teacher."</option><option value=student>".$Student."</option></select>";	
	}
elseif (isTeacher()){
	$selection = "<select name=authority id=authority><option value=>".$SelectEllipsis."</option><option value=teacher>".$Teacher."</option><option value=student>".$Student."</option></select>";
	}
elseif (isStudent()){
	header("Location: unauthorized.php");
	exit;	
	}

require_once "_html_parts.php";
	HTML_Render_Head();

echo $CSS_Main;

echo $JS_JQuery;

echo $JS_CreateUserName;

 HTML_Render_Body_Start(); ?>

<br /><p class="em" align="right"><<?php echo $RequiredFieldsAsterisk;?></p><?php echo "$msg"; ?>
	  <h1><?php echo $AddNewUser; ?></h1>
      <p>&nbsp;</p>
      <FORM METHOD="POST" ACTION="do_adduser.php" onsubmit="return false">
      <table width="600" border="0" cellspacing="0" cellpadding="0">
        <tr>
		  <td width="95" valign="top"><strong><?php echo $FirstName; ?></strong></td>
          <td width="157" valign="top"><input type="text" name="firstname" maxlength="50" /></td>
          <td width="348" valign="top"><span class="required">*</span></td>
        </tr>
        <tr>
          <td valign="top">&nbsp;</td>
          <td width="157" valign="top">&nbsp;</td>
          <td width="348" valign="top">&nbsp;</td>
        </tr>
        <tr>
		  <td width="95" valign="top"><strong><?php echo $LastName; ?></strong></td>
          <td valign="top"><input type="text" name="lastname" maxlength="50" onBlur="GetLastName()" /></td>
          <td valign="top"><span class="required">*</span></td>
        </tr>
        <tr>
          <td valign="top">&nbsp;</td>
          <td valign="top">&nbsp;</td>
          <td valign="top">&nbsp;</td>
        </tr>
        <tr>
		  <td width="95" valign="top"><strong><?php echo $UserName; ?></strong></td>
          <td valign="top"><input type="text" id="username" name="username" maxlength="25" />
          <div class="em"></div></td>
		  <td valign="top"><span class="em"><span id="user-result"></span><span class="required">*</span><?php echo $FirstLastNameExample; ?></span></td>
        </tr>
        <tr>
          <td valign="top">&nbsp;</td>
          <td valign="top">&nbsp;</td>
          <td valign="top">&nbsp;</td>
        </tr>
        <tr>
		  <td width="95" valign="top"><strong><?php echo $Password; ?></strong></td>
          <td valign="top"><input type="text" name="password" id="password" maxlength=25 /><div class="em"></div></td>
          <td valign="top"><span class="em"><span class="required">*</span><?php echo $CaseSensitive; ?></span></td>
        </tr>
        <tr>
          <td valign="top">&nbsp;</td>
          <td valign="top">&nbsp;</td>
          <td valign="top">&nbsp;</td>
        </tr>
        <tr>
		  <td width="95" valign="top"><strong><?php echo $Authority; ?> </strong></td>
          <td valign="top"><?php echo "$selection"; ?><span class="required">*</span></td>
          <td valign="top">&nbsp;</td>
        </tr>
        <tr>
          <td valign="top">&nbsp;</td>
          <td valign="top">&nbsp;</td>
          <td valign="top">&nbsp;</td>
        </tr>
        <tr>
          <td width="95" valign="top"><input type="SUBMIT" name="submit" value="<?php echo $AddUser;?>" class="button"/></td>
          <td valign="top"><input type="reset" name="Reset" value="<?php echo $Cancel; ?>" class="button" style="width: auto" /></td>
          <td valign="top">&nbsp;</td>
          
        </tr>
      </table></FORM>
      <div id="checkStatus" hidden="true"></div>
      <p>&nbsp;</p>
      <p>&nbsp;</p>
      <p>&nbsp;</p>
      <p>&nbsp;</p>
      <p>&nbsp;</p>
      <p>&nbsp;</p>
    
	<?php HTML_Render_Body_End(); ?>
    
<script type= "text/javascript">
$(document).ready(function(){


$("#username").blur(function (e){
	var username = $("#username").val();
    $.post("check_username.php",{ username :username}, function(data){
    $("#user-result").html(data);
	if(data == '<img src="images/not-available.png" />'){
		$("#checkStatus").html(1);
	}
	else
	{
		$("#checkStatus").html(0);
	}
    });
});


$("#lastname").blur(function (e){
	var username = $("#username").val();
    $.post("check_username.php",{ username :username}, function(data){
    $("#user-result").html(data);
	if(data == '<img src="images/not-available.png" />'){
	$("#checkStatus").html(1);
	}
	else
	{
		$("#checkStatus").html(0);
	}
    });
});

$("form").submit(function(e){

if(($("#firstname").val())==""){
		alert("Please enter your First Name");
		return false;
	}
if(($("#lastname").val())==""){
		alert("Please enter your Last Name");
		return false;
	}
if(($("#username").val())==""){
		alert("Please enter a Username");
		return false;
	}

if(($("#password").val())==""){
		alert("Please enter a Password");
		return false;
	} 
if(($("#authority").val())==""){
		alert("Please Select an Authority");
		return false;
	}

if($("#checkStatus").html()==1)
{
	alert("Username already exists. Please choose a different one.");
	return false;	
}

});
});
</script>