<?php
require '../connect.php'; 
if ($_POST['message'] && $_POST['chatid']) {
	$text = "Сообщение от учителя: ".htmlspecialchars($_POST['message']);
	$url="https://api.telegram.org/bot1846272684:AAGjCyxFiDWofColX8Sez8IWL_tWWQ6zP8Y/sendMessage?chat_id=".htmlspecialchars($_POST['chatid'])."&text=".$text;
	echo "<iframe src='".$url."' width=0 height=0></iframe>";
	echo "<center><h2>ОТПРАВЛЕНО</h2></center>";
} elseif ($_POST['message'] && $_POST['idgroup']) {
	$res = $conn->query("SELECT * FROM bot_spisok WHERE idgroup='".$_POST['idgroup']."'");
	$pps = $res->fetch_all(MYSQLI_ASSOC);
	foreach ($pps as $pp) { 
		$text = "Сообщение от учителя: ".htmlspecialchars($_POST['message']);
		$url="https://api.telegram.org/bot1846272684:AAGjCyxFiDWofColX8Sez8IWL_tWWQ6zP8Y/sendMessage?chat_id=".htmlspecialchars($pp['chatid'])."&text=".$text;
		echo "<iframe src='".$url."' width=0 height=0></iframe>";
	}
	sleep(3);
	echo "<center><h2>ОТПРАВЛЕНО</h2></center>";
} else echo "<center><h2>ЗАПОЛНИТЕ ВСЕ ПОЛЯ</h2></center>";
mysqli_close($conn);
 ?>

<!-- 
<script>
function closeWindow(){
			window.close();
}
</script>
<html>
<body onload="closeWindow()">

</body>
</html>
-->
