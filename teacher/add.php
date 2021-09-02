<?php
			if (isset($_GET['content'])) {
				switch ($_GET['content']) {
					case 'togroup': 
						require '../connect.php';
						if (isset($_GET['class'])) {
							echo "<p>Выберите ученика</p>";
							echo "<form action='?job=view&content=group&id=".$_GET['id']."' method='post'>";
							$res = $conn->query("SELECT * FROM bot_spisok WHERE class='".$_GET['class']."' ORDER BY second_name");
							$rows = $res->fetch_all(MYSQLI_ASSOC);
							if ($rows) {
								echo '<ol>';
								foreach ($rows as $row ) {
									echo "<li><input type='hidden' name='idgroup' value='".$_GET['id']."'><input type='checkbox' name='chatidtogroup[]' value='".$row['chatid']."'>".$row['second_name']." ".$row['first_name']."</li>";
								}
								echo '</ol><input type="submit" value="Добавить"></form>';
							}
						} else {
							echo "<p>Выберите класс</p>";
							$res = $conn->query("SELECT * FROM bot_razdel WHERE parent=0 ORDER BY razdel");
							$rows = $res->fetch_all(MYSQLI_ASSOC);
							if ($rows) { 
								foreach ($rows as $row) { 
									echo "<p><a href='?job=add&content=togroup&id=".$_GET['id']."&class=".$row['razdel']."'>".$row['razdel']."</a></p>";
								} 
							}
						}	
						mysqli_close($conn);
						break;
					case 'class': ?>
						<p>Класс: цифра параллели, например, 7</p>
						<form action='' method='post'><input type='hidden' name='proverka' value='1'>Класс: <input name='class' value=''maxlength='5'> <input type='submit' value='добавить'></form>
					<?php
						break;
					case 'theme':?>
						<p>Код темы: в формате 7-1, где 7 - класс, 1 - номер темы в планировании<br />Название темы: название темы из планиорвания, например, Архитектура ПК<br />Оба поля обязательны!</p>
						<form action='' method='post'><input type='hidden' name='proverka' value='2'>
						<p>Код темы: <input name='theme_code' value=''></p>
						<p>Название темы: <input name='theme_name' value=''></p>
						<p><input type='submit' value='добавить'></p</form>
					<?php
						break;
					case 'level':?>
						<p>Код темы: в формате 7-1, где 7 - класс, 1 - номер темы в планировании<br />Ссылка: ссылка на документ в облаке с описанием данного уровня<br />Все поля обязательны!</p>
						<form action='' method='post'><input type='hidden' name='proverka' value='3'>
						<p>Код темы: <input name='theme_code' value=''></p>
						<p><input type="radio" name="level" value="tb"> Теория обязательная</p>
						<p><input type="radio" name="level" value="ts"> Теория основная</p>
						<p><input type="radio" name="level" value="td"> Теория дополнительная</p>
						<p><input type="radio" name="level" value="zb"> Задания обязательные</p>
						<p><input type="radio" name="level" value="zs"> Задания основные</p>
						<p><input type="radio" name="level" value="zd"> Задания дополнительные</p>
						<p>Ссылка: <input name='link' value=''></p>
						<p><input type='submit' value='добавить'></p</form>
					<?php
						break;
					case 'group': ?>
						<p>Группа: любое название группы, например, 7а, Факультатив такой-то и т.д.</p>
						<form action='' method='post'><input type='hidden' name='proverka' value='1'>Группа: <input name='group' value=''maxlength='500'> <input type='submit' value='добавить'></form>
					<?php
						break;
					case 'pass': ?>
						<p>Введите дважды пароль бота</p>	
						<form action="" method="post" name="form"><input type='hidden' name='proverka' value='2'>
							<input style="width:150px;" name="passDB1" type="password" placeholder="Пароль">
							<br><br>
							<input style="width:150px;" name="passDB2" type="password" placeholder="Ещё раз пароль">
							<br><br><input type="submit" value="Сохранить">
						</form>		
					<?php
						break;
				}
			}	
