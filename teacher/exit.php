<?php
session_start();//стартуем сессию
unset ($_SESSION['$logSESS']);//удаляем зарегистрированную глобальную переменную
session_destroy();//уничтожаем сессию
header("location: index.php");//перебрасываем на главную страницу пользовательской части блога
exit;
?>