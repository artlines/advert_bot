<!DOCTYPE HTML>
<html>
  <head>
    <meta charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?=config('main_title');?> || Авторизация</title>
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="/assets/admin/css/main.css">
  </head>
  <body>
    <div id="auth_block" class="d-flex justify-content-center align-items-center">
      <form action="/login" name="auth" method="post">
        <div class="row mb-2">
          <div class="col">
            <input type="text" name="username" class="form-control" placeholder="Логин" />
          </div>
        </div>
        <div class="row mb-4">
          <div class="col">
            <input type="password" name="password" class="form-control" placeholder="Пароль" />
          </div>
        </div>
        <div class="row mb-2">
          <div class="col">
            <input type="submit" value="Войти" class="btn btn-outline-primary btn-block">
          </div>
        </div>
        <div class="row">
          <div class="col">
            <?=$error_message;?>
          </div>
        </div>
      </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js"></script>
  </body>
</html>

