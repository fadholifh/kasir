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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE pesanan SET pembeli=%s, makanan=%s, jumlah=%s, minuman=%s, jumlah1=%s, total=%s WHERE id=%s",
                       GetSQLValueString($_POST['pembeli'], "text"),
                       GetSQLValueString($_POST['makanan'], "text"),
                       GetSQLValueString($_POST['jumlah'], "int"),
                       GetSQLValueString($_POST['minuman'], "text"),
                       GetSQLValueString($_POST['jumlah1'], "int"),
                       GetSQLValueString($_POST['total'], "int"),
                       GetSQLValueString($_POST['id'], "int"));

  mysql_select_db($database_koneksi, $koneksi);
  $Result1 = mysql_query($updateSQL, $koneksi) or die(mysql_error());

  $updateGoTo = "tampil.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE pesanan SET pembeli=%s, makanan=%s, jumlah=%s, minuman=%s, jumlah1=%s, total=%s WHERE id=%s",
                       GetSQLValueString($_POST['pembeli'], "text"),
                       GetSQLValueString($_POST['makanan'], "text"),
                       GetSQLValueString($_POST['jumlah'], "int"),
                       GetSQLValueString($_POST['minuman'], "text"),
                       GetSQLValueString($_POST['jumlah1'], "int"),
                       GetSQLValueString($_POST['total'], "int"),
                       GetSQLValueString($_POST['id'], "int"));

  mysql_select_db($database_koneksi, $koneksi);
  $Result1 = mysql_query($updateSQL, $koneksi) or die(mysql_error());

  $updateGoTo = "tampil.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$colname_pesanan = "-1";
if (isset($_GET['id'])) {
  $colname_pesanan = $_GET['id'];
}
mysql_select_db($database_koneksi, $koneksi);
$query_pesanan = sprintf("SELECT * FROM pesanan WHERE id = %s", GetSQLValueString($colname_pesanan, "int"));
$pesanan = mysql_query($query_pesanan, $koneksi) or die(mysql_error());
$row_pesanan = mysql_fetch_assoc($pesanan);
$totalRows_pesanan = mysql_num_rows($pesanan);

mysql_select_db($database_koneksi, $koneksi);
$query_makanan = "SELECT * FROM makanan";
$makanan = mysql_query($query_makanan, $koneksi) or die(mysql_error());
$row_makanan = mysql_fetch_assoc($makanan);
$totalRows_makanan = mysql_num_rows($makanan);

mysql_select_db($database_koneksi, $koneksi);
$query_minuman = "SELECT * FROM minuman";
$minuman = mysql_query($query_minuman, $koneksi) or die(mysql_error());
$row_minuman = mysql_fetch_assoc($minuman);
$totalRows_minuman = mysql_num_rows($minuman);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Edit Transaksi</title>
<script>
function hitung(){
	var makanan = document.getElementById("makanan").value;
	var makanan_jumlah = document.getElementById("makanan_jumlah").value;
	
	var minuman = document.getElementById("minuman").value;
	var minuman_jumlah = document.getElementById("minuman_jumlah").value;
	var total = makanan*makanan_jumlah+minuman*minuman_jumlah;
	document.getElementById("total").value=total;
}
</script>
<link href="css/style.css" rel="stylesheet" type="text/css" />
<link rel="shortcut icon" href="img/kasir.png" />
</head>

<body>
<div class="container">
<form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1">
  <table align="center">
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Pembeli:</td>
      <td><input type="text" name="pembeli" value="<?php echo htmlentities($row_pesanan['pembeli'], ENT_COMPAT, 'utf-8'); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Makanan:</td>
      <td><select name="makanan" id="makanan">
        <?php 
do {  
?>
        <option value="<?php echo $row_makanan['harga']?>" <?php if (!(strcmp($row_makanan['harga'], htmlentities($row_pesanan['makanan'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>><?php echo $row_makanan['nama']?></option>
        <?php
} while ($row_makanan = mysql_fetch_assoc($makanan));
?>
      </select></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Jumlah:</td>
      <td><input type="text" name="jumlah" value="<?php echo htmlentities($row_pesanan['jumlah'], ENT_COMPAT, 'utf-8'); ?>" size="32" id="makanan_jumlah" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Minuman:</td>
      <td><select name="minuman" id="minuman">
        <?php 
do {  
?>
        <option value="<?php echo $row_minuman['harga']?>" <?php if (!(strcmp($row_minuman['harga'], htmlentities($row_pesanan['minuman'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>><?php echo $row_minuman['nama']?></option>
        <?php
} while ($row_minuman = mysql_fetch_assoc($minuman));
?>
      </select></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Jumlah1:</td>
      <td><input type="text" name="jumlah1" value="<?php echo htmlentities($row_pesanan['jumlah1'], ENT_COMPAT, 'utf-8'); ?>" size="32" id="minuman_jumlah" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Total:</td>
      <td><input type="text" name="total" value="<?php echo htmlentities($row_pesanan['total'], ENT_COMPAT, 'utf-8'); ?>" size="32"  id="total" placeholder="Total"/><input name="button" type="button" value="+" onClick="hitung();"/></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">&nbsp;</td>
      <td><input type="submit" value="Simpan" /></td>
    </tr>
  </table>
  <input type="hidden" name="MM_update" value="form1" />
  <input type="hidden" name="id" value="<?php echo $row_pesanan['id']; ?>" />
</form>
</div>
</body>
</html>
<?php
mysql_free_result($pesanan);

mysql_free_result($makanan);

mysql_free_result($minuman);
?>
