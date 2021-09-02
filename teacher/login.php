<?php

//АВТОРИЗАЦИЯ
//уничтожаем переменную с логином и паролем которые были созданы путем ввода их в строку
if (isset ($_GET['loginDB'])) {$login = $_GET['loginDB'];unset($login);}
if (isset ($_GET['passDB'])) {$pass = $_GET['passDB'];unset($pass);}

$passDB = $_POST['passDB']; //***********************
$loginDB = $_POST['loginDB']; //****************************

if(isset($loginDB) AND isset($passDB))//если существуют логин и пароль
{	
       $prov = getenv('HTTP_REFERER');//определяем страницу с который пришел запрос
        $prov = str_replace("www.","",$prov);//удаляем www если есть
        preg_match("/(http\:\/\/[-a-z0-9_.]+\/)/",$prov,$prov_pm);//чистим адресс от лишнего, нам необходимо добиться ссылки вот такого вида http://xxxx.ru
        $prov = $prov_pm[1];//заносим чистый адрес в отдельную переменную
        $server_root = str_replace("www.","",$server_root);//удаляем www если есть


        if($server_root == $prov)//если адрес нашего блога и адрес страницы с которой был прислан зарос равны
        {
			
			$passDB = md5($passDB);//шифруем введенный пароль
            require '../connect.php';	
            $res = $res = $conn->query("SELECT * FROM bot_admin WHERE login='".$loginDB."'");//выводим из базы данных логин и пароль
  			$rows = $res->fetch_all(MYSQLI_ASSOC);
            
            if ($rows) {
				foreach ($rows as $row) {
					$pass = $row['pass'];
				}

				if ($passDB == $pass) //если введенная информация совпадает с информацией из БД
                {
                   session_start();//стартуем сессию
      				$_SESSION['$logSESS'] = $loginDB;//создаем глобальную переменную
      				header("location: index.php");//переносим пользователя на главную страницу
      				exit;				
                }
                else//если введеная инфо не совпадает с инфо из БД
                {
                    header("location: login.php");//переносим на форму авторизации
                    exit; 				
                }
            }
            else//если не найдено такого юзера в БД
            {
                header("location: login.php");//переносим на форму авторизации
                exit;
            }
        }
        else//если запрос был послан с другого адреса
        {
            header("location: login.php");//переносим на форму авторизации
            exit; 			
        }
}

?>
<table width="600px" cellpadding="0" cellspacing="0" border="0" align="center">
    <tr>
        <td valign="top" align="center">		
        <form action="" method="post">
            <br>
            <input style="width:150px;" name="loginDB" type="text" placeholder="Логин">
            <br>
            <input style="width:150px;" name="passDB" type="password" placeholder="Пароль">
            <br><br><input type="submit" value="Авторизация">
        </form>		
        </td>
    </tr>
</table>