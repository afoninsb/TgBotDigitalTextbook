<?php
if (!$_POST['loginDB'] || !$_POST['passDB1'] || !$_POST['passDB2']) $message = "Заполните все поля. <a href='../teacher/signupadmin.php'>Вернуться</a>";
elseif ($_POST['passDB1'] != $_POST['passDB2']) $message = "Пароли не совпадают. <a href='../teacher/signupadmin.php'>Вернуться</a>";
else {
	$pass = md5($_POST['passDB1']);
	require '../connect.php';
	$conn->query("INSERT INTO bot_admin (login, pass) VALUES ('".$_POST['loginDB']."', '".$pass."')");
	mysqli_close($conn);
	$message = "Готово! Вы админ бота <a href='../teacher/'>Войти в админку</a>";
}

?>
<html>
<body>
<?php
 if ($message) echo '<center><h2>'.$message.'</h2></center>';
?>
<table width="600px" cellpadding="0" cellspacing="0" border="0" align="center">
    <tr>
        <td valign="top" align="center">		
        <form action="" method="post" name="form">
            <br><br>
            <input style="width:150px;" name="loginDB" type="text" placeholder="Логин">
            <br><br>
            <input style="width:150px;" name="passDB1" type="password" placeholder="Пароль">
            <br><br>
            <input style="width:150px;" name="passDB2" type="password" placeholder="Ещё раз пароль">
            <br><br><input type="submit" value="Регистрация">
        </form>		
        </td>
    </tr>
</table>
</body>
</html>


