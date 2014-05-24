<?php session_start();?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="keywords" content=", clockworkwebviewer, clockwork, web viewer, trurascalz, webviewer,">
    <meta name="author" content="Alex Savory">
<meta name="description" content="A web viewer  which is free and provided by Alex Savory">

    <!-- Le styles -->
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <style type="text/css">
      body {
        padding-top: 20px;
        padding-bottom: 60px;
      }

      /* Custom container */
      .container {
        margin: 0 auto;
        max-width: 1000px;
      }
      .container > hr {
        margin: 60px 0;
      }

      /* Main marketing message and sign up button */
      .jumbotron {
        margin: 80px 0;
        text-align: center;
      }
      .jumbotron h1 {
        font-size: 100px;
        line-height: 1;
      }
      .jumbotron .lead {
        font-size: 24px;
        line-height: 1.25;
      }
      .jumbotron .btn {
        font-size: 21px;
        padding: 14px 24px;
      }

      /* Supporting marketing content */
      .marketing {
        margin: 60px 0;
      }
      .marketing p + h4 {
        margin-top: 28px;
      }


      /* Customize the navbar links to be fill the entire space of the .navbar */
      .navbar .navbar-inner {
        padding: 0;
      }
      .navbar .nav {
        margin: 0;
        display: table;
        width: 100%;
      }
      .navbar .nav li {
        display: table-cell;
        width: 1%;
        float: none;
      }
      .navbar .nav li a {
        font-weight: bold;
        text-align: center;
        border-left: 1px solid rgba(255,255,255,.75);
        border-right: 1px solid rgba(0,0,0,.1);
      }
      .navbar .nav li:first-child a {
        border-left: 0;
        border-radius: 3px 0 0 3px;
      }
      .navbar .nav li:last-child a {
        border-right: 0;
        border-radius: 0 3px 3px 0;
      }
    </style>
    <link href="assets/css/bootstrap-responsive.css" rel="stylesheet">

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="assets/js/html5shiv.js"></script>
    <![endif]-->

    <!-- Fav and touch icons -->
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="assets/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="assets/ico/apple-touch-icon-114-precomposed.png">
      <link rel="apple-touch-icon-precomposed" sizes="72x72" href="assets/ico/apple-touch-icon-72-precomposed.png">
                    <link rel="apple-touch-icon-precomposed" href="assets/ico/apple-touch-icon-57-precomposed.png">
                                   <link rel="shortcut icon" href="assets/ico/favicon.png">
  </head>

  <body>

    <div class="container">

      <?php include('header.php');?>

      <!-- Jumbotron -->
       <?php 
       if (!isset($_SESSION['steamlogin'])) {
header("Location:index.php");
       }

$steamid = $_SESSION['steamame'];
$con = mysql_connect($address,$user,$pass);
if (!$con)
  {
  echo('<div class="alert alert-error">' . mysql_error() . '</div>');
  }
else echo('<div class="alert alert-success">Successfully Connected</div>');

mysql_select_db($database, $con);

$perpage = 5;

if(isset($_GET["page"]))

{

$page = intval($_GET["page"]);

}

else

{

$page = 1;

}

$calc = $perpage * $page;

$start = $calc - $perpage;

$result = mysql_query("SELECT *  FROM `characters` WHERE `_Schema` = '".$gamemodecode."' AND  `_SteamID` LIKE  '".$_SESSION['steamname']."' ORDER BY _Name Limit $start, $perpage");
if (mysql_error()==""){ 
echo '<div class="alert alert-success">Successfully Queried</div>'; 
}
else

echo '<div class="alert alert-warning">'.mysql_error().'</div>';
echo "
<center>
<table class='table table-striped'>
<h1>".$steampersona."'s Characters</h1>
<tr>
<th>Name</th>
<th>Last Played</th>
<th>Action</th>
</tr>
</center>
";


$rows = mysql_num_rows($result);

if($rows)

{

$i = 0;

}

while($row = @mysql_fetch_array($result))
  {
$lastplayed = $row['_LastPlayed'];
$steam = $row['_SteamID'];
$parsed = SteamID2CommunityID($steam);

  echo "<tr>";
  echo "<td>" . $row['_Name'] . "</td>";
  echo "<td>" . date('r', $lastplayed) . "</td>";
  echo '<td> <form action="edit.php" method="post" style="display: inline;">
    <input type="hidden" name="action" value="edit" />
  <input type="hidden" name="key" value="'.$row['_Key'].'" />
  <button type="submit" class="btn btn-inverse">Edit</button>
    </form>
<form action="edit.php" method="post" style="display: inline;">
  <input type="hidden" name="action" value="delete" />
  <input type="hidden" name="key" value="'.$row['_Key'].'" />
    <input type="hidden" name="name" value="'.$row['_Name'].'" />
  <button type="submit" class="btn btn-danger" onclick="return confirm(\'Are you sure you want to delete \n'.$row['_Name'].'?\')" >Delete</button>
    </form>



    </td>';
  echo "</tr>";
  }
echo "</table>";
echo"<div class='pagination pagination-large'>";
echo"  <ul>";

if(isset($page))

{

$result = mysql_query("select Count(*) As Total from characters WHERE `_Schema` = '".$gamemodecode."' AND  `_SteamID` LIKE  '".$_SESSION['steamname']."'");

$rows = mysql_num_rows($result);

if($rows)

{

$rs = mysql_fetch_array($result);

$total = $rs["Total"];

}

$totalPages = ceil($total / $perpage);

if($page <=1 )

{

echo "<li class='disabled'><a href='#'>&laquo;</a></li>";

}

else

{

$j = $page - 1;

echo "    <li><a href='{$_SERVER['PHP_SELF']}?page=$j'>&laquo;</a></li>";

}

for($i=1; $i <= $totalPages; $i++)

{

if($i<>$page)

{

echo "    <li><a href='{$_SERVER['PHP_SELF']}?page=$i'>$i</a></li>";

}

else

{

echo "<li class='active'><a href='#'>$i</a></li>";

}

}

if($page == $totalPages )

{

echo "    <li class='disabled'><a href='#'>&raquo;</a></li>";

}

else

{

$j = $page + 1;

echo "<li><a href='{$_SERVER['PHP_SELF']}?page=$j'>&raquo;</a></li>";

}

}
echo "</ul>";
echo"</div>";

?>

      <hr>

   <?php include('footer.php')?>

    </div> <!-- /container -->

    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="assets/js/jquery.js"></script>
    <script src="assets/js/bootstrap-transition.js"></script>
    <script src="assets/js/bootstrap-alert.js"></script>
    <script src="assets/js/bootstrap-modal.js"></script>
    <script src="assets/js/bootstrap-dropdown.js"></script>
    <script src="assets/js/bootstrap-scrollspy.js"></script>
    <script src="assets/js/bootstrap-tab.js"></script>
    <script src="assets/js/bootstrap-tooltip.js"></script>
    <script src="assets/js/bootstrap-popover.js"></script>
    <script src="assets/js/bootstrap-button.js"></script>
    <script src="assets/js/bootstrap-collapse.js"></script>
    <script src="assets/js/bootstrap-carousel.js"></script>
    <script src="assets/js/bootstrap-typeahead.js"></script>

  </body>
</html>
