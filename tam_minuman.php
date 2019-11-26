<?php require_once('Connections/koneksi.php'); ?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "";
$MM_donotCheckaccess = "true";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
  // For security, start by assuming the visitor is NOT authorized. 
  $isValid = False; 

  // When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
  // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
  if (!empty($UserName)) { 
    // Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
    // Parse the strings into arrays. 
    $arrUsers = Explode(",", $strUsers); 
    $arrGroups = Explode(",", $strGroups); 
    if (in_array($UserName, $arrUsers)) { 
      $isValid = true; 
    } 
    // Or, you may restrict access to only certain users based on their username. 
    if (in_array($UserGroup, $arrGroups)) { 
      $isValid = true; 
    } 
    if (($strUsers == "") && true) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}

$MM_restrictGoTo = "login.php";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0) 
  $MM_referrer .= "?" . $_SERVER['QUERY_STRING'];
  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: ". $MM_restrictGoTo); 
  exit;
}
?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}

$currentPage = $_SERVER["PHP_SELF"];

$maxRows_Minuman = 10;
$pageNum_Minuman = 0;
if (isset($_GET['pageNum_Minuman'])) {
  $pageNum_Minuman = $_GET['pageNum_Minuman'];
}
$startRow_Minuman = $pageNum_Minuman * $maxRows_Minuman;

mysql_select_db($database_koneksi, $koneksi);
$query_Minuman = "SELECT * FROM minuman";
$query_limit_Minuman = sprintf("%s LIMIT %d, %d", $query_Minuman, $startRow_Minuman, $maxRows_Minuman);
$Minuman = mysql_query($query_limit_Minuman, $koneksi) or die(mysql_error());
$row_Minuman = mysql_fetch_assoc($Minuman);

if (isset($_GET['totalRows_Minuman'])) {
  $totalRows_Minuman = $_GET['totalRows_Minuman'];
} else {
  $all_Minuman = mysql_query($query_Minuman);
  $totalRows_Minuman = mysql_num_rows($all_Minuman);
}
$totalPages_Minuman = ceil($totalRows_Minuman/$maxRows_Minuman)-1;

$queryString_Minuman = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_Minuman") == false && 
        stristr($param, "totalRows_Minuman") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_Minuman = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_Minuman = sprintf("&totalRows_Minuman=%d%s", $totalRows_Minuman, $queryString_Minuman);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Tampil Minuman</title>
<link href="css/style.css" rel="stylesheet" type="text/css" />
<link rel="shortcut icon" href="img/kasir.png" />
</head>

<body>
<div class="contabel">
<center>Tampil Minuman</center>
<center><p>&nbsp;
<table border="0">
  <tr>
    <td><?php if ($pageNum_Minuman > 0) { // Show if not first page ?>
        <a href="<?php printf("%s?pageNum_Minuman=%d%s", $currentPage, 0, $queryString_Minuman); ?>">First</a>
    <?php } // Show if not first page ?></td>
    <td><?php if ($pageNum_Minuman > 0) { // Show if not first page ?>
        <a href="<?php printf("%s?pageNum_Minuman=%d%s", $currentPage, max(0, $pageNum_Minuman - 1), $queryString_Minuman); ?>">Previous</a>
    <?php } // Show if not first page ?></td>
    <td><?php if ($pageNum_Minuman < $totalPages_Minuman) { // Show if not last page ?>
        <a href="<?php printf("%s?pageNum_Minuman=%d%s", $currentPage, min($totalPages_Minuman, $pageNum_Minuman + 1), $queryString_Minuman); ?>">Next</a>
    <?php } // Show if not last page ?></td>
    <td><?php if ($pageNum_Minuman < $totalPages_Minuman) { // Show if not last page ?>
        <a href="<?php printf("%s?pageNum_Minuman=%d%s", $currentPage, $totalPages_Minuman, $queryString_Minuman); ?>">Last</a>
    <?php } // Show if not last page ?></td>
  </tr>
</table>
</p>
<table border="1">
  <tr>
    <td>id</td>
    <td>nama</td>
    <td>harga</td>
    <td>Action</td>
  </tr>
  <?php do { ?>
    <tr>
      <td><?php echo $row_Minuman['id']; ?></td>
      <td><?php echo $row_Minuman['nama']; ?></td>
      <td><?php echo $row_Minuman['harga']; ?></td>
      <td><a href="dminuman.php?id=<?php echo $row_Minuman['id']; ?>">Delete</a> | <a href="eminuman.php?id=<?php echo $row_Minuman['id']; ?>">Edit</a></td>
    </tr>
    <?php } while ($row_Minuman = mysql_fetch_assoc($Minuman)); ?>
</table>
</center>
</div>
<div class="lain">
	<a class="box" href="index.php" target="_blank">Transaksi</a>
    <a class="box" href="logout.php">Log Out</a>
    <a class="box" href="tminuman.php" target="_blank">(+) Minuman</a>
    <a class="box" href="tmakanan.php" target="_blank">(+) Makanan</a>
</div>
</body>
</html>
<?php
mysql_free_result($Minuman);
?>
