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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO pesanan (id, pembeli, makanan, jumlah, minuman, jumlah1, total) VALUES (%s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['id'], "int"),
                       GetSQLValueString($_POST['pembeli'], "text"),
                       GetSQLValueString($_POST['makanan'], "text"),
                       GetSQLValueString($_POST['jumlah'], "int"),
                       GetSQLValueString($_POST['minuman'], "text"),
                       GetSQLValueString($_POST['jumlah1'], "int"),
                       GetSQLValueString($_POST['total'], "int"));

  mysql_select_db($database_koneksi, $koneksi);
  $Result1 = mysql_query($insertSQL, $koneksi) or die(mysql_error());

  $insertGoTo = "tampil.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

$maxRows_pesanan = 10;
$pageNum_pesanan = 0;
if (isset($_GET['pageNum_pesanan'])) {
  $pageNum_pesanan = $_GET['pageNum_pesanan'];
}
$startRow_pesanan = $pageNum_pesanan * $maxRows_pesanan;

mysql_select_db($database_koneksi, $koneksi);
$query_pesanan = "SELECT * FROM pesanan";
$query_limit_pesanan = sprintf("%s LIMIT %d, %d", $query_pesanan, $startRow_pesanan, $maxRows_pesanan);
$pesanan = mysql_query($query_limit_pesanan, $koneksi) or die(mysql_error());
$row_pesanan = mysql_fetch_assoc($pesanan);

if (isset($_GET['totalRows_pesanan'])) {
  $totalRows_pesanan = $_GET['totalRows_pesanan'];
} else {
  $all_pesanan = mysql_query($query_pesanan);
  $totalRows_pesanan = mysql_num_rows($all_pesanan);
}
$totalPages_pesanan = ceil($totalRows_pesanan/$maxRows_pesanan)-1;

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
<title>Transaksi</title>
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
<center>Transaksi</center>
<form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1">
  <table align="center">
    <tr valign="baseline">
      <td nowrap="nowrap" align="left">Pembeli</td>
      <td><input type="text" name="pembeli" value="" size="32" placeholder="Pembeli" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="left">Makanan</td>
      <td><select name="makanan"  id="makanan">
        <?php
do {  
?>
        <option value="<?php echo $row_makanan['harga']?>"><?php echo $row_makanan['nama']?></option>
        <?php
} while ($row_makanan = mysql_fetch_assoc($makanan));
  $rows = mysql_num_rows($makanan);
  if($rows > 0) {
      mysql_data_seek($makanan, 0);
	  $row_makanan = mysql_fetch_assoc($makanan);
  }
?>
      </select></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="left">Jumlah</td>
      <td><input type="text" name="jumlah" value="" size="32" id="makanan_jumlah" placeholder="Jumlah Makanan" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="left">Minuman</td>
      <td><select name="minuman" id="minuman">
        <?php
do {  
?>
        <option value="<?php echo $row_minuman['harga']?>"><?php echo $row_minuman['nama']?></option>
        <?php
} while ($row_minuman = mysql_fetch_assoc($minuman));
  $rows = mysql_num_rows($minuman);
  if($rows > 0) {
      mysql_data_seek($minuman, 0);
	  $row_minuman = mysql_fetch_assoc($minuman);
  }
?>
      </select></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="left">Jumlah</td>
      <td><input type="text" name="jumlah1" value="" size="32" id="minuman_jumlah" placeholder="Jumlah Minuman"/></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="left">Total</td>
      <td><input type="text" name="total" value="" size="32"  id="total" placeholder="Total *(Klik Hitung)"/><input name="button" type="button" value="Hitung" onClick="hitung();"/></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="left">&nbsp;</td>
      <td><input type="submit" value="Tambah" /></td>
    </tr>
  </table>
  <input type="hidden" name="MM_insert" value="form1" />
</form>
</div>
<div class="lain">
	<a class="box" href="tampil.php" target="_blank">Tampil</a>
    <a class="box" href="logout.php">Log Out</a>
    <a class="box" href="tmakanan.php" target="_blank">(+) Makanan</a>
    <a class="box" href="tminuman.php" target="_blank">(+) Minuman</a>
</div>
</body>
</html>
<?php
mysql_free_result($pesanan);

mysql_free_result($makanan);

mysql_free_result($minuman);
?>
