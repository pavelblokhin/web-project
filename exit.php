<?php
session_start();

// Удаляем все переменные сессии
$_SESSION = array();

// Если нужно уничтожить сессию, также удаляем сессионную куку
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Наконец, уничтожаем сессию
session_destroy();

// Перенаправляем пользователя на главную страницу или страницу входа
header("Location: welcome.php");
exit;
?>