<?php
//СКРИПТ ПРОВЕРКИ АВТОРИЗАЦИИ
if(isset($_GET['logSESS'])) {$logSESS = $_GET['logSESS'];unset($logSESS);}
if(isset($_POST['logSESS'])) {$logSESS = $_POST['logSESS'];unset($logSESS);}

  session_start();
  $logSESS = $_SESSION['$logSESS'];
  if(!isset($logSESS))
  {
    header("location: login.php");
    exit;  
  }






$message = [];
$pr = 0;
require '../connect.php';
if ($_POST['class']) {
	$pr++;
	$res = $conn->query("SELECT * FROM bot_razdel WHERE razdel = '".$_POST['class']."'");
	$rows = $res->fetch_all(MYSQLI_ASSOC);
	if ($rows) {
		$message = ["Такой класс уже есть!", "red"];
	} else {
		$conn->query("INSERT INTO bot_razdel (razdel, parent, content) VALUES ('".$_POST['class']."', '0', '".$_POST['class']."')");
		$message = ["Добавлено!", "green"];
	}
} elseif ($_POST['theme_code'] && $_POST['theme_name']) {
	$res = $conn->query("SELECT * FROM bot_razdel WHERE razdel = '".$_POST['theme_code']."'");
	$rows = $res->fetch_all(MYSQLI_ASSOC);
	if ($rows) {
		$message = ["Такая тема уже есть!", "red"];
	} else {
		$th = explode('-', $_POST['theme_code']);
		$conn->query("INSERT INTO bot_razdel (razdel, parent, content) VALUES ('".$_POST['theme_code']."', '".$th[0]."', '".$_POST['theme_name']."')");
		$message = ["Добавлено!", "green"];
	}
} elseif ($_POST['group']) {
	$res = $conn->query("SELECT * FROM bot_group WHERE name = '".$_POST['group']."'");
	$rows = $res->fetch_all(MYSQLI_ASSOC);
	if ($rows) {
		$message = ["Такая группа уже есть!", "red"];
	} else {
		$pr++;
		$conn->query("INSERT INTO bot_group (name) VALUES ('".$_POST['group']."')");
		$message = ["Добавлено!", "green"];
	}
} elseif ($_POST['theme_code'] && $_POST['level'] && $_POST['link']) {
	$razd = $_POST['theme_code']."-".$_POST['level'];
	$res = $conn->query("SELECT * FROM bot_razdel WHERE razdel = '".$razd."'");
	$rows = $res->fetch_all(MYSQLI_ASSOC);
	if ($rows) {
		$message = ["Такой уровень уже есть!", "red"];
	} else {
		$conn->query("INSERT INTO bot_razdel (razdel, parent, content) VALUES ('".$razd."', '".$_POST['theme_code']."', '".$_POST['link']."')");
		$message = ["Добавлено!", "green"];
	}
} elseif ($_POST['del']) {
	if ($_POST['all'] == 1) {
		if (stripos($_POST['del'], '-')) {
			$conn->query("DELETE FROM bot_razdel WHERE parent = '".$_POST['del']."'");
		} else {
			$res = $conn->query("SELECT * FROM bot_razdel WHERE parent = '".$_POST['del']."'");
			$rows = $res->fetch_all(MYSQLI_ASSOC);
			if ($rows) { 
				foreach ($rows as $row) { 
					$conn->query("DELETE FROM bot_razdel WHERE parent = '".$row['razdel']."'");
				} 
			}
			$conn->query("DELETE FROM bot_razdel WHERE razdel = '".$row['razdel']."'");
		}
	}
	$conn->query("DELETE FROM bot_razdel WHERE razdel = '".$_POST['del']."'");
	$message = ["Удалено!", "green"];
} elseif ($_POST['delgroup']) {	
	if ($_POST['chatid']) {
		$conn->query("UPDATE bot_spisok SET idgroup='' WHERE chatid='".$_POST['chatid']."'");
		$message = ["Удален!", "green"];
	} else {
		if ($_POST['all'] == 1) {    
			$conn->query("UPDATE bot_spisok SET idgroup='' WHERE idgroup='".$_POST['delgroup']."'");
		}
		$conn->query("DELETE FROM bot_group WHERE id = '".$_POST['delgroup']."'");
		$message = ["Удалено!", "green"];
	}
} elseif ($_POST['delpupil'] && !$_POST['delfrombot']) {	
	$conn->query("UPDATE bot_spisok SET class='0', ok='2' WHERE chatid='".$_POST['delpupil']."'");
	$message = ["Удален!", "green"];
} elseif ($_POST['delfrombot']) {	
	$conn->query("DELETE FROM bot_spisok WHERE chatid = '".$_POST['delpupil']."'");
	$message = ["Удален!", "green"];
} elseif ($_POST['chatidtogroup']) {
	foreach ($_POST['chatidtogroup'] as $chatid) {
		$conn->query("UPDATE bot_spisok SET idgroup='".$_POST['idgroup']."' WHERE chatid='".$chatid."'");
	}
	$message = ["Добавлены!", "green"];
} elseif ($_POST['idgroup'] && $_POST['message']) {
	foreach ($_POST['chatidtogroup'] as $chatid) {
		$conn->query("UPDATE bot_spisok SET idgroup='".$_POST['idgroup']."' WHERE chatid='".$chatid."'");
	}
	$message = ["Добавлены!", "green"];
} elseif ($_POST['passDB1'] && $_POST['passDB2']) {
	if ($_POST['passDB1'] == $_POST['passDB2']) {
		$res = $conn->query("SELECT * FROM bot_admin WHERE login = 'bot'");
		$rows = $res->fetch_all(MYSQLI_ASSOC);
		if ($rows) {
			$conn->query("UPDATE bot_admin SET pass='".$_POST['passDB1']."' WHERE login = 'bot'");
			$message = ["Пароль бота изменён!", "green"];
		} else {
			$conn->query("INSERT INTO bot_admin (login,pass) VALUES ('bot','".$_POST['passDB1']."')");
			$message = ["Пароль бота добавлен!", "green"];
		}
	} else $message = ["Пароли не совпадают!", "red"];
}
	mysqli_close($conn);

if ($_POST['passDB1']) $pr++;
if ($_POST['passDB2']) $pr++;
if ($_POST['theme_code']) $pr++;
if ($_POST['theme_name']) $pr++;
if ($_POST['level']) $pr++;
if ($_POST['link']) $pr++;
if ($pr != $_POST['proverka'] && !$message) $message = ["Заполните все поля!", "red"];
