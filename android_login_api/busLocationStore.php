<?php
require_once 'include/DB_Functions.php';
$db = new DB_Functions();

$response = array("error" => FALSE);


if (isset($_POST['bus_no']) && isset($_POST['latitude']) && isset($_POST['longitude']))
{
  $bus_no = $_POST['bus_no'];
  $latitude = $_POST['latitude'];
  $longitude = $_POST['longitude'];


  if($db->DriverLocationStore($bus_no, $latitude, $longitude, $db->isBusRegistered($bus_no)))
  {

    $response["error"] = FALSE;
    $response["message"] = "Sucess";
    echo json_encode($response);
  }
  else {
    $response["error"] = TRUE;
    $response["message"] = "Unknown error occurred in storing location!";
    echo json_encode($response);
      }

    }
    else{
      $response["error"] = TRUE;
      $response["message"] = "Required parameters (bus_no,latitude, longitude) is missing!";
      echo json_encode($response);
    }

?>
