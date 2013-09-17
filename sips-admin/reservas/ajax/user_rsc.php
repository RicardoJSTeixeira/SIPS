<?php
require_once('../func/reserve_utils.php');
$username=(isset($_POST["username"]))?$_POST["username"]:false;
$password=(isset($_POST["password"]))?$_POST["password"]:false;
$rsc=(isset($_POST["rsc"]))?$_POST["rsc"]:false;

if(!($username and $password and $rsc)){exit;}

$user=new users;

if(!$user->active){exit;}

if($user->getUser($username)){
    echo json_encode("exist");
}

$query="Select user_group from vicidial_user_groups where user_group like '".$user->user_group."\_%'";
$result=mysql_query($query) or die(mysql_error());
$num=mysql_num_rows($result);

$new_user_group=sprintf($user->user_group."_%1$03d",$num+1);


$query="Select user_group from vicidial_user_groups where user_group = '$new_user_group'";
$result=mysql_query($query) or die(mysql_error());
$exist=mysql_num_rows($result);

$limit=0;
while($exist>0 and $limit<10){
    $limit++;
    $num++;
    $new_user_group=sprintf($user->user_group."_%1$03d",$num+1);
    $query="Select user_group from vicidial_user_groups where user_group = '$new_user_group'";
    $result=mysql_query($query) or die(mysql_error());
    $exist=mysql_num_rows($result);
}

if(!$exist){
    $user->newUserGroup($new_user_group, $user->user_group." Cal");
    $user->newAdmin($username, $password, $new_user_group);
    $query="INSERT INTO  `sips_admin_links` (`url` ,`imgpath` ,`label` ,`grupo`)VALUES ('reservas/views/calendar_container.php?rsc=$rsc',  '/images/icons/calendar_32.png',  'Calendário',  '$new_user_group');";
    $result=mysql_query($query) or die(mysql_error());
echo json_encode(true);
}
else{
    echo json_encode(false);
}
?>
