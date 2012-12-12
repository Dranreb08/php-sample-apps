<?php
/**
* @file
* Script to load the home page
*
* This load the option on the home page and shows the list of options available
* on the system. This also loads the corresponding file according to the
* request
*/
/**
* Generate home page
*/
function home_page(){
  $tpl = new Savant3();
  $menuoptions = array(
    array(
      '#href'       => '?p=addItem',
      'description' => 'Add new item',
      'hover'       => 'Click to add a new item'
    ),
    array(
      '#href'       => '?p=billing',
      'description' => 'Billing',
      'hover'       => 'Click to bill a new customer'
    ),
    array(
      '#href'       => '?p=update',
      'description' => 'List inventory',
      'hover'       => 'Click to list the inventory and update stock'
    ),
    array(
      '#href'       => '?p=billing&view',
      'description' => 'View Bill',
      'hover'       => 'Click to view the bill details'
    )
    );
  $name = 'Welcome';

  $tpl->title = $name;
  $tpl->menuoptions = $menuoptions;
  $tpl->display('index.php.tpl');
}

session_start();

require_once 'Savant3.php';
require_once './config.php';
require_once './auth.inc';


$dbh = new PDO("mysql:host=localhost;dbname=" . $DB_NAME, $DB_USER, $DB_PASSWORD);
$tpl = new Savant3();
// Check if $_GET['p'] is set. If not, set it.
if(!isset($_GET['p'])){
  $_GET['p'] = "billing";
}

if(!isset($_SESSION['user'])){
  require_once("authenticate.php");
  display_login_form();
}
else if($_GET['p'] == 'logout'){
  require_once("authenticate.php");
  logout();
}
else if($_GET['p'] == "addItem"){
  require_once("./additem.php");
  if(isset($_POST['submit'])){
  process_form();
  }
  else {
    display_form();
  }
}
else if($_GET['p'] == "update" && in_array("items",$auth[$_SESSION["role"]])){

  require_once("./update.php");
  if(isset($_POST['submit'])){
  update_item($_POST['code'],$_POST['amount']);
  }
  else if(isset($_POST['rateUpdate'])){
    update_rate($_POST['code'],$_POST['rate']);
  }
  else if(isset($_GET['search'])){
   display_item($_GET['search']);
  }
  else if(isset($_POST['block'])){
    block_items();
  }
  else {
  display_item();
  }
}
  else if($_GET['p'] == 'home'){
  home_page();
}
else {

  require_once("./billing.php");


  if(isset($_POST['Bill'])){
    bill();
  }
  else if(isset($_GET['view'])){
    if($_GET['view'] != NULL){
      display_bill($_GET['view']);
    }
    else {
      global $tpl;
      $tpl->title = "Bill Details";
      $tpl->display("billno.php.tpl");
    }
  }
  else
  {
    generate_bill();
  }
}

