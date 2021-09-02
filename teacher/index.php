<?php
	require 'post.php';			
?>
<html>
<title>Управление чат-ботом</title>
<body>
<div style="margin-left:100px; margin-right:100px;">
	<a href="exit.php" style="font-size:12px;font-weight:100;">(Выход)</a> | <a href="?job=add&content=pass" style="font-size:12px;font-weight:100;">Установить пароль бота</a><br>
		<table>
			<tr style="font-weight: bold;">
				<td width=300>Материалы</td>
				<td width=300>Учащиеся</td>
				<td width=300>Сообщения</td>
			</tr>
			<tr>
				<td> - <a href="?job=add&content=class">Добавить класс</a></td>
				<td> - <a href="?job=pupil&content=list">Списки по классам</a></td>
				<td> - <a href="?job=pupil&content=message">Отправить личное сообщение</a></td>
			</tr>
			<tr>
				<td> - <a href="?job=add&content=theme">Добавить тему</a></td>
				<td> - <a href="?job=add&content=group">Добавить группу учащихся</a></td>
				<td> - <a href="?job=pupil&content=groupmessage">Отправить сообщение группе</a></td>
			</tr>
			<tr>
				<td> - <a href="?job=add&content=level">Добавить уровень</a></td>
				<td> - <a href="?job=view&content=group">Группы учащихся</a></td>
				<td></td>
			</tr>
			<tr>
				<td> - <a href="?job=view&content=class">Классы и материалы</a></td>
				<td> - <a href="?job=pupil&content=task">Непроверенные работы</a></td>
				<td></td>
			</tr>
		</table>
	<hr />

	<?php
	if ($message) {
		echo "<h2 style='font-weight: bold; color:".$message[1]."'>".$message[0]."<br /></h2>";
	}
	if (isset($_GET['job'])) {
		switch ($_GET['job']) {
			case 'add':
				require 'add.php';			
				break;
			case 'view':
				require 'view.php';			
				break;
			case 'pupil':
				require 'pupil.php';			
				break;
		}
	}	
	?>

</div>

</body>
</html>