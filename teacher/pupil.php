<?php
if (isset($_GET['content'])) {
	require '../connect.php';
	switch ($_GET['content']) {
		case 'task':
			echo '<p>Непроверенные работы</p>';
			$res = $conn->query("SELECT * FROM bot_work WHERE provereno<>'да'");
			$rows = $res->fetch_all(MYSQLI_ASSOC);
			print ("<table>");
			foreach ($rows as $row ) {
				$profile = $conn->query("SELECT * FROM bot_spisok WHERE chatid = '".$row['chatid']."'");
				$prof = $profile->fetch_all(MYSQLI_ASSOC);
				foreach ($prof as $pr ) {
					print ("<tr><td>".$row['time']." | ".$pr['first_name']." ".$pr['second_name']." | ".$pr['class']." | <a href=".$row['url']." target=_blank>работа</a> | </td><td><form action='provereno.php' method='post' target='_blank'><input name='idwork' value='".$row['idwork']."' readonly> - <input maxlength='500' size='50' name='review' value=''><input type='hidden' name='time' value='".$row['time']."'><input type='hidden' name='chatid' value='".$row['chatid']."'><input type='submit' value='Проверено'></form></td></tr>");
				}
			}
			print ("</table>");
			break;
		case 'list':
			if (!$_GET['class']) {
				echo "<p>Выберите класс</p>";
				$res = $conn->query("SELECT * FROM bot_razdel WHERE parent=0 ORDER BY razdel");
				$rows = $res->fetch_all(MYSQLI_ASSOC);
				if ($rows) { 
					foreach ($rows as $row) { 
						echo "<p><a href='?job=pupil&content=list&class=".$row['razdel']."'>".$row['razdel']."</a></p>";
					} 
				}
			} else {
				echo "<p>Список ".$_GET['class']." класса</p>";
				$res = $conn->query("SELECT * FROM bot_spisok WHERE class='".$_GET['class']."' ORDER BY second_name");
				$rows = $res->fetch_all(MYSQLI_ASSOC);
				if ($rows) {
					echo '<ol><table>';
					foreach ($rows as $row ) {
						echo "<tr><td><li><form action='' method='post'><input type='hidden' name='proverka' value='0'><input type='hidden' name='delpupil' value='".$row['chatid']."'>".$row['chatid']." - ".$row['second_name']." ".$row['first_name']." | Тема: ".$row['theme']."</li></td><td>&nbsp;&nbsp;&nbsp; <input type='submit' value='X' title='Удалить из класса'>&nbsp;&nbsp;&nbsp;</td><td><input type='submit' value='УДАЛИТЬ ИЗ БОТА' name='delfrombot'></td></tr>";
					}
					echo '</table></ol>';
				}
			}
			break;
		case 'message':
			echo '<p>Отправка сообщения ученику</p>'; ?>
			<form action='message.php' method='post' target='_blank'>
			<p>ID чата в Telegram: <input maxlength='30' size='20' name='chatid' value=''></p>
			<p>Сообщение: <input maxlength='500' size='50' name='message' value=''></p>
			<input type='submit' value='Отправить'></form>
			<?php
			break;
		case 'groupmessage':
			echo '<p>Отправка сообщения группе</p>'; ?>
			<form action='message.php' method='post' target='_blank'>
			<table><tr>
			<td width=200>Выберите группу
			<?php
			$res = $conn->query("SELECT * FROM bot_group");
			$rows = $res->fetch_all(MYSQLI_ASSOC);
			if ($rows) {
				foreach ($rows as $row ) {
					echo "<p><input type='radio' name='idgroup' value='".$row['id']."'>".$row['name']."</p>";
				}
			}
			?>
			</td>
			<td width=500>Сообщение: <input maxlength='500' size='50' name='message' value=''>
			<input type='submit' value='Отправить'></form></tr></table>
			<?php
			
			break;
	}
}	
mysqli_close($conn);
