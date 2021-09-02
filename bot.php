<?php

// определяем кодировку
header('Content-type: text/html; charset=utf-8');
// Создаем объект бота
$bot = new Bot();
// Обрабатываем пришедшие данные
$bot->init('php://input');

/**
 * Class Bot
 */
class Bot
{
    // <bot_token> - созданный токен для нашего бота от @BotFather
    private $botToken = "1858997684:AAGhfJtjjygjhofColX8Sez8IWL_tW7hbs4jP8Y";
    // адрес для запросов к API Telegram
    private $apiUrl = "https://api.telegram.org/bot";

    public function init($data)
    {
		require 'connect.php';
		
		
		// создаем массив из пришедших данных от API Telegram
        $arrData = $this->getData($data);
		
        if (array_key_exists('message', $arrData)) {
            $chat_id = $arrData['message']['chat']['id'];
            $message = $arrData['message']['text'];

        } elseif (array_key_exists('callback_query', $arrData)) {
            $chat_id = $arrData['callback_query']['message']['chat']['id'];
            $message = $arrData['callback_query']['data'];
        }

		$justKeyboard = $this->getKeyBoard([[["text" => "Темы"]],
			[["text" => "Сдать работу"], ["text" => "Вопрос учителю"]]
		]);
		
		$profile = $this->getProfile($chat_id);
		if (!$profile) {
			$dataSend = array(
				'text' => "Введите пароль",
				'chat_id' => $chat_id,
			);
			$this->requestToTelegram($dataSend, "sendMessage");
			$conn->query("INSERT INTO bot_spisok (chatid, first_name, second_name, class, ok, theme, level, idgroup) VALUES ('".$chat_id."', '0', '0', '0', '0', '0', '0', '0')");
		} else {
			switch ($profile['ok']) {
				case '0':
					$res = $conn->query("SELECT * FROM bot_admin WHERE login = 'bot'");
					$rows = $res->fetch_all(MYSQLI_ASSOC);
					if ($rows) {
						foreach ($rows as $row) { 
							$pswd = $row['pass'];
						} 
					}
					if ($message!=$pswd) {
						$dataSend = array(
							'text' => "Введите пароль",
							'chat_id' => $chat_id,
						);
						$this->requestToTelegram($dataSend, "sendMessage");
					} else {
						$dataSend = array(
							'text' => "Отлично. Теперь давайте знакомиться. Введите Ваше имя.",
							'chat_id' => $chat_id,
						);
						$conn->query("UPDATE bot_spisok SET ok='1' WHERE chatid='".$chat_id."'");
						$this->requestToTelegram($dataSend, "sendMessage");
					}
					break;
				case '1':
					$txt = "А теперь, ".$message.", введите Вашу фамилию.";
					$dataSend = array(
						'text' => $txt,
						'chat_id' => $chat_id,
					);
					$conn->query("UPDATE bot_spisok SET first_name='".$message."', ok='2' WHERE chatid='".$chat_id."'");
					$this->requestToTelegram($dataSend, "sendMessage");			
					break;
				case '2':
					$txt = "Теперь, ".$profile['first_name'].", выберите Ваш класс.";
					$dataSend = array(
						'text' => $txt,
						'chat_id' => $chat_id,
						'reply_markup' => $this->getInlineKeyBoard($this->stroimKbd(0, 4)),
					);
					if ($profile['second_name'] == '0') 
						$conn->query("UPDATE bot_spisok SET second_name='".$message."', ok='3' WHERE chatid='".$chat_id."'");
					else 
						$conn->query("UPDATE bot_spisok SET ok='3' WHERE chatid='".$chat_id."'");
					$this->requestToTelegram($dataSend, "sendMessage");			
					break;
				case '3':
					$txt = "Спасибо, ".$profile['first_name'].". 
					
					Нажми кнопку 'Темы', чтобы выбрать тему для изучения.";
					$dataSend = array(
						'text' => $txt,
						'chat_id' => $chat_id,
						'reply_markup' => $justKeyboard,
					);
					$conn->query("UPDATE bot_spisok SET class='".$message."', ok='4' WHERE chatid='".$chat_id."'");
					$this->requestToTelegram($dataSend, "sendMessage");	
					break;
				case '4':
					switch ($message) {
						case 'Темы':
							$profile = $this->getProfile($chat_id);
							$dataSend = array(
								'text' => 'Выберите тему по информатике '.$profile['class'].' класса.',
								'chat_id' => $chat_id,
								'reply_markup' => $justKeyboard,
							);
							$this->requestToTelegram($dataSend, "sendMessage");
							$dataSend = array(
								'text' => 'Темы:',
								'chat_id' => $chat_id,
								'reply_markup' => $this->getInlineKeyBoard($this->stroimKbd($profile['class'], 1)),
							);
							$this->requestToTelegram($dataSend, "sendMessage");
							break;
						case '/help':
							$dataSend = array(
								'text' => "Значения кнопок:",
								'chat_id' => $chat_id,
							);
							$this->requestToTelegram($dataSend, "sendMessage");
							break;
						case (preg_match('/^Задания/', $message) ? true : false):
						case (preg_match('/^Теория/', $message) ? true : false):
							$conn->query("UPDATE bot_spisok SET level='".$message."' WHERE chatid='".$chat_id."'");
							$dataSend = array(
								'text' => $message.': '.$this->getLink($message, $chat_id),
								'chat_id' => $chat_id,
							);
							$this->requestToTelegram($dataSend, "sendMessage");
							break;
						case 'Вопрос учителю':
							$dataSend = array(
								'text' => 'Чтобы отправить вопрос учителю, отправьте сообщение в следющем формате: #вопрос текст_вопроса. Например,
								#вопрос можно я доделаю практическую работу на следующем уроке?',
								'chat_id' => $chat_id,
							);
							$this->requestToTelegram($dataSend, "sendMessage");
							break;				
						case (preg_match('/^#вопрос/', $message) ? true : false):
							$profile = $this->getProfile($chat_id);
							$message = str_replace("#вопрос ", "", $message);
							$message = 'Чат: '.$chat_id.' ||| Имя: '.$profile['first_name'].' ||| Фамилия: '.$profile['second_name'].' ||| Класс: '.$profile['class'].' ||| Вопрос: '.$message;
							$dataSend = array(
								'text' => $message,
								'chat_id' => '391741304',
							);
							$this->requestToTelegram($dataSend, "sendMessage");
							break;
						case (preg_match('/^#ответ/', $message) ? true : false): // $ответ$391741304$текст ответа
							$data = explode('#', $message);
							$dataSend = array(
								'text' => 'Ответ учителя: '.$data[3],
								'chat_id' => $data[2],
							);
							$this->requestToTelegram($dataSend, "sendMessage");
							break;
						case 'Сдать работу':
							$dataSend = array(
								'text' => 'Чтобы сдать работу:
								1) загрузите вашу работу в любое облачное хранилище (Google Drive, Яндекс Диск и т.п.)
								2) создайте ссылку на документ с возможностью редактирования
								3) отправьте в этот чат сообщение в формате: #работа#ID_работы#ссылка_на_ваш_документ
								
								ID работы написан в начале текста работы.',
								'chat_id' => $chat_id,
							);
							$this->requestToTelegram($dataSend, "sendMessage");
							break;				
						case (preg_match('/^#работа#/', $message) ? true : false):
							$mes = explode('#', $message);
							$today = date("Y-m-d H:i:s");
							$conn->query("INSERT INTO bot_work (time, chatid, idwork, url) VALUES ('".$today."', '".$chat_id."', '".$mes[2]."', '".$mes[3]."')");
							$dataSend = array(
								'text' => 'Ваша работа принята',
								'chat_id' => $chat_id,
							);
							$this->requestToTelegram($dataSend, "sendMessage");
							break;				
						default:
							$n=substr_count($message, '-');
							if ($n==1) {
								if ($this->themeInBase($message) == 1) {  
									$conn->query("UPDATE bot_spisok SET theme='".$message."' WHERE chatid='".$chat_id."'");
									$dataSend = array(
										'text' => 'Выберите, чем заняться. Поработать с теорией или порешать задачки?
										
										Тема: '.$this->themeTxt($message, $chat_id),
										'chat_id' => $chat_id,
										'reply_markup' => $this->getKeyBoard($this->stroimKbdLevel($message)),
									);
								}
							} else {
								$dataSend = array(
									'text' => $message,
									'chat_id' => $chat_id,
									'reply_markup' => $justKeyboard,
								);
							}
							$this->requestToTelegram($dataSend, "sendMessage");
							break;
						}
			}
		} 
		mysqli_close($conn);
	}

    /**
     * создаем inline клавиатуру
     * @return string
     */
    private function getInlineKeyBoard($data)
    {
        $inlineKeyboard = array(
            "inline_keyboard" => $data,
        );
        return json_encode($inlineKeyboard);
    }

    /**
     * создаем клавиатуру
     * @return string
     */
    private function getKeyBoard($data)
    {
        $keyboard = array(
            "keyboard" => $data,
            "one_time_keyboard" => false,
            "resize_keyboard" => true
        );
        return json_encode($keyboard);
    }

    /**
     * Парсим что приходит преобразуем в массив
     * @param $data
     * @return mixed
     */
    private function getData($data)
    {
        return json_decode(file_get_contents($data), TRUE);
    }

    /** Отправляем запрос в Телеграмм
     * @param $data
     * @param string $type
     * @return mixed
     */
    private function requestToTelegram($data, $type)
    {
        $result = null;

        if (is_array($data)) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->apiUrl . $this->botToken . '/' . $type);
            curl_setopt($ch, CURLOPT_POST, count($data));
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            $result = curl_exec($ch);
            curl_close($ch);
        }
        return $result;
    }
		
	private	function themeInBase($data)
    {
        require 'connect.php';
		$result = 0;
		$res = $conn->query("SELECT * FROM bot_razdel WHERE razdel = '".$data."'");
		$rows = $res->fetch_all(MYSQLI_ASSOC);
		if ($rows) $result=1;
		mysqli_close($conn); 
        return $result;
    }
	
	private	function getProfile($chat_id)
    {
        require 'connect.php';
		$res = $conn->query("SELECT * FROM bot_spisok WHERE chatid = '".$chat_id."'");
		$rows = $res->fetch_all(MYSQLI_ASSOC);
		foreach ($rows as $row ) {
			$result = $row; 
			break;
		}
		mysqli_close($conn); 
        return $result;
   }
   
   	private	function stroimKbd($razd, $kolStr)
    {
		$result = [];
		$str = [];
		$ks = 0;
		
        require 'connect.php';
		$res = $conn->query("SELECT * FROM bot_razdel WHERE parent = '".$razd."'");
		$rows = $res->fetch_all(MYSQLI_ASSOC);		
		foreach($rows as $row) {
			$knopka = ['text' => $row['content'], 'callback_data' => $row['razdel']];
			array_push($str, $knopka);
			unset($knopka);
			$ks++;
			if ($ks == $kolStr) {
				array_push($result, $str);
				$ks = 0;
				unset($str);
				$str = [];
			}
		}
		if ($ks > 0) array_push($result, $str);
		mysqli_close($conn); 
		return $result;
	}
	
	private	function themeTxt($data, $chat_id)
    {
		$profile = $this->getProfile($chat_id);
        require 'connect.php';
		$res = $conn->query("SELECT * FROM bot_razdel WHERE parent = '".$profile['class']."'");
		$rows = $res->fetch_all(MYSQLI_ASSOC);		
		foreach($rows as $row) {
			if ($data == $row['razdel']) {
				$result = $row['content'];
				break;
			}
		}
		mysqli_close($conn);
		return $result;
	}

	private	function getLink($data, $chat_id)
    {
		$profile = $this->getProfile($chat_id);
		switch ($data) {
			case 'Теория обязательная':
				$data = $profile['theme'].'-tb';
				break;
			case 'Теория основная':
				$data = $profile['theme'].'-ts';
				break;
			case 'Теория дополнительная':
				$data = $profile['theme'].'-td';
				break;
			case 'Задания обязательные':
				$data = $profile['theme'].'-zb';
				break;
			case 'Задания основные':
				$data = $profile['theme'].'-zs';
				break;
			case 'Задания дополнительные':
				$data = $profile['theme'].'-zd';
				break;
		}	
		require 'connect.php';
		$res = $conn->query("SELECT * FROM bot_razdel WHERE razdel = '".$data."'");
		$rows = $res->fetch_all(MYSQLI_ASSOC);		
		foreach($rows as $row) {
			$result = $row['content'];
		}
		mysqli_close($conn);
		return $result;
	} 

	private	function stroimKbdLevel($data)
    {
		$str = [];
		$result = [];
		require 'connect.php';
		$res = $conn->query("SELECT * FROM bot_razdel WHERE parent = '".$data."'");
		$rows = $res->fetch_all(MYSQLI_ASSOC);		
		foreach ($rows as $row) { 
			if (stripos($row['razdel'], 'tb')) {
				$txt='Теория обязательная';
				$knopka1 = ['text' => $txt];
			} elseif (stripos($row['razdel'], 'ts')) {
				$txt='Теория основная';
				$knopka2 = ['text' => $txt];
			} elseif (stripos($row['razdel'], 'td')) {
				$txt='Теория дополнительная';
				$knopka3 = ['text' => $txt];
			} elseif (stripos($row['razdel'], 'zb')) {
				$txt='Задания обязательные';
				$knopka4 = ['text' => $txt];
			} elseif (stripos($row['razdel'], 'zs')) {
				$txt='Задания основные';
				$knopka5 = ['text' => $txt];
			} elseif (stripos($row['razdel'], 'zd')) {
				$txt='Задания дополнительные';
				$knopka6 = ['text' => $txt];
			}
		}
		if ($knopka1) array_push($str, $knopka1);
		if ($knopka2) array_push($str, $knopka2);
		if ($knopka3) array_push($str, $knopka3);
		if ($str) array_push($result, $str);
		unset($str);
		$str = [];
		if ($knopka4) array_push($str, $knopka4);
		if ($knopka5) array_push($str, $knopka5);
		if ($knopka6) array_push($str, $knopka6);
		if ($str) array_push($result, $str);
		unset($str);
		$str = [];
		$str = [['text' => 'Темы']];
		array_push($result, $str);
		$str = 	[["text" => "Сдать работу"], ["text" => "Вопрос учителю"]];
		array_push($result, $str);
		mysqli_close($conn);
		return $result;
	} 


}


















