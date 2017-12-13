<!doctype html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?=config('main_title');?> || Админпанель</title>
    <link rel="shortcut icon" href="/favicon.ico" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="/assets/admin/css/main.css" />
    <link rel="stylesheet" type="text/css" href="/node_modules/font-awesome/css/font-awesome.min.css" />
    <link rel="stylesheet" type="text/css" href="/node_modules/lightbox2/dist/css/lightbox.min.css" />
  </head>
  <body>

    <ul class="nav" id="main_nav">
      <li class="nav-item">
        <a class="nav-link" href="/admin/adverts">Объявления</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="/admin/category">Категории</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="/admin/users">Пользователи</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="/admin/geo">География</a>
      </li>
      <!--li class="nav-item">
        <a class="nav-link" href="/admin/settings">Настройки</a>
      </li>
      <<li class="nav-item">
        <a class="nav-link" href="/admin/bot">Бот</a>
      </li>-->
    </ul>

    <div id="content"> <?=$text;?> </div>

    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js"></script>
    <script src="/node_modules/lightbox2/dist/js/lightbox.min.js"></script>
    <script src="/assets/admin/js/main.js"></script>
  </body>
</html>