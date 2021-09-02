<?php
if (isset($_GET['content'])) {
	require '../connect.php';
	if ($_GET['content'] == 'group') {
		if (!$_GET['id']) {?>
			<p>Группы</p> 
			<?php
			$res = $conn->query("SELECT * FROM bot_group");
			$rows = $res->fetch_all(MYSQLI_ASSOC);
			if ($rows) {
				foreach ($rows as $row) { ?>
					<form action='' method='post'><input type='hidden' name='proverka' value='0'><input type='hidden' name='delgroup' value='<?=$row['id'];?>'><a href='?job=view&content=group&id=<?=$row['id'];?>'><?=$row['name'];?></a>&nbsp;&nbsp;&nbsp;<input type='submit' value='X' title='Удалить группу'> | <input type="checkbox" name="all" value="1"> Также удалить ВСЕ данные этой группы</form>
				<?php
				}
			}
		} else {
			$res = $conn->query("SELECT * FROM bot_group WHERE id='".$_GET['id']."'");
			$grs = $res->fetch_all(MYSQLI_ASSOC);
			if ($grs) {
				foreach ($grs as $gr) { 
					echo "Состав группы ".$gr['name']." | <a href='?job=add&content=togroup&id=".$_GET['id']."'>добавить учащихся</a>";
					echo '<ol><table>';
					$res = $conn->query("SELECT * FROM bot_spisok WHERE idgroup='".$_GET['id']."' ORDER BY second_name");
					$pps = $res->fetch_all(MYSQLI_ASSOC);
					if ($pps) {
						foreach ($pps as $pp) { 
							echo "<tr><td><li><form action='' method='post'><input type='hidden' name='proverka' value='0'><input type='hidden' name='delgroup' value='".$_GET['id']."'><input type='hidden' name='chatid' value='".$pp['chatid']."'>".$pp['second_name']." ".$pp['first_name']." (".$pp['class']." класс)</li></td><td>&nbsp;&nbsp;&nbsp;<input type='submit' value='X' title='Удалить из группы'></form></td></tr>";
						}
					}
					echo "</tabe></ol>";
				}
			}
			
		}
	} elseif ($_GET['content'] == 'class') {
		if (isset($_GET['theme'])) {
			$res = $conn->query("SELECT * FROM bot_razdel WHERE razdel='".$_GET['theme']."'");
			$txts = $res->fetch_all(MYSQLI_ASSOC);
			if ($txts) {
				foreach ($txts as $txt) {
					echo "<p>Уровни темы '".$txt['content']."' (".$_GET['theme'].") ".$_GET['class']." класса</p>";
					$res = $conn->query("SELECT * FROM bot_razdel WHERE parent='".$_GET['theme']."' ORDER BY razdel");
					$infs = $res->fetch_all(MYSQLI_ASSOC);
					if ($infs) {
						foreach ($infs as $inf) {
							if (stripos($inf['razdel'], 'tb')) $txt='Теория обязательная';
							elseif (stripos($inf['razdel'], 'ts')) $txt='Теория основная';
							elseif (stripos($inf['razdel'], 'td')) $txt='Теория дополнительная';
							elseif (stripos($inf['razdel'], 'zb')) $txt='Задания обязательные';
							elseif (stripos($inf['razdel'], 'zs')) $txt='Задания основные';
							elseif (stripos($inf['razdel'], 'zd')) $txt='Задания дополнительные';
							echo "<form action='' method='post'><input type='hidden' name='proverka' value='0'><input type='hidden' name='del' value='".$inf['razdel']."'><input type='hidden' name='lclass' value='".$_GET['class']."'><input type='hidden' name='ltheme' value='".$_GET['theme']."'>".$inf['razdel']." ".$txt." <a href='".$inf['content']."' target='_blank'>посмотреть</a>&nbsp;&nbsp;&nbsp;<input type='submit' value='X' title='Удалить уровень'></form>";
						}
					}
				}
			}			
		} elseif (isset($_GET['class'])) {
			$res = $conn->query("SELECT * FROM bot_razdel WHERE parent='".$_GET['class']."'");
			$infs = $res->fetch_all(MYSQLI_ASSOC);
			if ($infs) {
				echo "<p>Темы ".$_GET['class']." класса</p>";
				foreach ($infs as $inf) {
					echo "<form action='' method='post'><input type='hidden' name='proverka' value='0'><input type='hidden' name='del' value='".$inf['razdel']."'><input type='hidden' name='thclass' value='".$_GET['class']."'><a href='?job=view&content=class&class=".$_GET['class']."&theme=".$inf['razdel']."'>".$inf['razdel']." ".$inf['content']."</a>&nbsp;&nbsp;&nbsp;<input type='submit' value='X' title='Удалить тему'> | <input type='checkbox' name='all' value='1'> Также удалить ВСЕ уровни этой темы</form>";
				}
			}
		} else {
			$res = $conn->query("SELECT * FROM bot_razdel WHERE parent='0'");
			$rows = $res->fetch_all(MYSQLI_ASSOC);
			if ($rows) {
				foreach ($rows as $row) {
					echo "<form action='' method='post'><input type='hidden' name='proverka' value='0'><input type='hidden' name='del' value='".$row['razdel']."'><a href='?job=view&content=class&class=".$row['razdel']."'>".$row['razdel']."</a>&nbsp;&nbsp;&nbsp; <input type='submit' value='X' title='Удалить класс'> | <input type='checkbox' name='all' value='1'> Также удалить ВСЕ данные этого класса</form>";
				}
			}
		}
	}
	
	
	
	
	mysqli_close($conn);
}
