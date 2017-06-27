<?php
require_once 'include/DB_Functions.php';
$db = new DB_Functions();


$response = array("error" => FALSE);

if (isset($_POST['bus_no']))
{
  $bus_no = $_POST['bus_no'];
  $bus_location = $db->GetBusLoc($bus_no);
  if($bus_location != false)
  {
    $response["error"] =FALSE;
    $response["bus_no"]=$bus_location["bus_no"];
    $response["bus_location"]["latitude"] = $bus_location["latitude"];
    $response["bus_location"]["longitude"] = $bus_location["longitude"];
    echo json_encode($response);
  }
  else{
    $response["error"]=TRUE;
    $response["error_msg"]="Location Not found";
    echo json_encode($response);
  }
}
else{
  $response["error"] = TRUE;
  $response["error_msg"] = "Required parameters bus_no is missing!";
  echo json_encode($response);


}




 ?>
