<?php
@$dpt_id=$_GET['dpt_id'];
//$cat_id=2;
/// Preventing injection attack //// 
if(!is_numeric($dpt_id)){
echo "Data Error";
exit;
 }
/// end of checking injection attack ////

include ("dbconnect.php");

$sql="SELECT * FROM majors WHERE department_ID=?";
$row=$bdd->prepare($sql);
$row->execute(array($dpt_id));
$result=$row->fetchAll(PDO::FETCH_ASSOC);

$main = array('data'=>$result);
echo json_encode($main);
?>
