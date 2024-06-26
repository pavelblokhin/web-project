# web-project
WEB-приложение "Hotteling"

## Описание

данный проект представляет собой back-end и front-end приложение-игру, основанную на модели из микроэкономики - "Hotteling".
Экономическая игра имеет реализацию мультиплеера, где игроки соревнуются чья фирма станет успешной. На сайте реализована логики сессий и "аккаунтов", информация о которых хранится в базе данных.

## Hotteling

В оригинальной модели хотлинга рассматривается пример пляжа, вдоль которого размещены отели и 2ум продавцам предстоит решить где поставить их мазаничик. Сложность заключается в том, что покупатели имеют свою функцию полезности которая зависит от их затрат на перемещение (тоесть дойти до магазина) и стоимость продукции, и каждый покупатель решает идти ему покупать товар к одному продавцу, к другому или вовсе не покупать.
Таким образом продавцам стоит ещё и выбирать не только место но и цену чтобы к ним пришло максимальное количество людей

## Модель игры

Игра состоит из 20 раундов, по окончанию которых проверяется чья фирма получила больше прибыли. Игровкам известно функция полезности покупателей, но не отличие от оригинальной модели им не известно откуда к ним пойдут люди, ведь в отели заселяются разное количество человек и в разное время. Игрокам предстоит выбрать место где они будут торговать, количество продукции за которые они несут косты, а также цену за которую будут продавать товар, после этого идёт подсчёт прибыли и начинается новый раунд. Также в игре реализовано поле, с манхэттенской метрикой, чтобы играть было интереснее

## Стек технологий
* PHP
* HTML
* SQLite
* Java Script
* AJAX
* CSS
* Visual Studio Code

## Архитектура сайта
* Главная страница index.php с которой реализуется вход на сайт и регистрация
* registr.php - страница для регистрации
* logout - выйти из аккаунта
* welcome.php страница где пользователь либо создаёт новую игру либо присоединяется к созданной
* create_game.php - создание игры
* join_game.php - подключение к игре
* game.php основаня страница игры где прописано поле и основные элементы каждого раунда
* class.php файл с классами фирм и клеток для игры
* exit.php - выход из игры по её окончанию
* Остальные файлы upgrade_firm.php, update_player_state, select_cell.php и тд нужны для реализации действий игры с помощью AJAX запросов (коментарии к коду показывают для чего они нужны)

* ## База данных
База данных состоит из 2ух баз и нескольких таблиц:
* БД users и таблица user_data хранит данные аккаунтов пользователей
* БД game_db и таблицы:
  1) games - хранит основную информацию о последнем ходе игры по её id
  2) game_cells хранит в себе клетки покупателей которые будут появлятся у обоих игроков
  3) players_data хранит информацию о выбранной клетке каждым игоком

