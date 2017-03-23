<?php
//ru
return [
    'Simple users manager'      => 'Простой менеджер пользователей',
// controllers
//MainController.php
    'Confirm you registration on site'
                                => 'Подтвердите вашу регистрацию на сайте',
    'User created. Wait for admission.'
                                => 'Пользователь создан. Ждите подтверждения.',
    "Signup problem. Connect to support."
                                => 'При регистрации возникли проблемы. Свяжитесь со службой поддкржки.',
    'Profile modified.'         => 'Профайл изменен.',
    'Profile not change.'       => 'Профайл не менялся.',
    'Such unconfirmed user not found or already confirmed.'
                                => 'Такой неподтвержденный пользователь не найден или подтверждение уже произошло.',
    'Try to login or signup again.'
                                => 'Попробуйте войти или зарегиструйтесь снова.',
    'Token expired please register again.'
                                => 'Срок регистрации истек, зарегистрируйтесь снова.',
    'Registration confirmed.'   => 'Регистрация подтверждена.',
    'Wait moderation.'          => 'Ждите подтверждения модератором.',
    'User registration confirmation error.'
                                => 'Ошибка подтверждения регистрации',
    'Apply to support.'         => 'Обратитесь в службу поддержки',
    "To register go to 'Signup' link from our site menu."
                                => "Зарегистрироваться можно только кликнув на пункт меню 'Регистрация' нашего сайта.",

//AdminController.php
    'User {id} not found.'      => 'Пользователь {id} не найден.',
    'Status changed.'           => 'Статус изменен.',
    "Status didn't change."     => 'Статус не изменен.',
    "Error on field: '{field}'."=> "Ошибка в поле: '{field}'.",
    'User #{id} deleted.'       => 'Пользователь #{id} удален.',
    'Deletion user #{id} fail.' => 'Удалить пользователя #{id} не удалось.',

// models
//LoginForm.php
    'Incorrect username or password.'
                                => 'Неправильное имя или пароль',
  //'Password'                  => 'Пароль',
    'Remember me'               => 'Запомнить меня',
//ProfileForm.php
    'Password'                  => 'Пароль',
    'Old password'              => 'Старый пароль',
    'New password'              => 'Новый пароль',
    'Repeat password'           => 'Повторить пароль',
    'Old password required'     => 'Старый пароль необходим',
    'Invalid password'          => 'Неправильный пароль',
//User.php
    'wait'                      => 'ожидает',
    'registered'                => 'зарегистрированный',
    'unactive'                  => 'неактивный',
    'active'                    => 'активный',
    'Only latin letters, digits, hyphen, points and blanks begin with letter'
                                => 'Только латинские буквы, цифры, дефис, точки и пробелы, начиная с буквы',
    'ID'                        => 'Ид',
    'Username'                  => 'Имя пользователя',
    'Auth key'                  => 'Ключ аутентификации',
    'Change auth key'           => 'Изменить ключ аутентификации',
    'Email confirm token'       => 'Токен подтверждения Email',
    'Password hash'             => 'Хеш пароля',
    'Password reset token'      => 'Токен обновления пароля',
    'Email'                     => 'Email',
    'Status'                    => 'Статус',
    //'Created at'                => 'Создан в',
    'Created at'                => 'Создан',
    //'Updated at'                => 'Изменен в',
    'Updated at'                => 'Изменен',

// views/main
//login.php
    'Site login'                => 'Вход на сайт',
    'Login'                     => 'Вход',
//profile-form.php
    'Create new user'           => 'Создать нового пользователя',
    'Update profile'            => 'Изменить профайл',
    'Site'                      => 'Сайт',
    'User name'                 => "Имя пользователя",
    'Enter code here'           => 'Введите код',
    'Click to refresh code'     => 'Кликните, чтобы изменить код',
    'Send'                      => 'Отправить',

// views/admin
//_form.php
    'Create'                    => 'Создать',
    'Update'                    => 'Обновить',
    'Roles'                     => 'Роли',
//_search.php
    'Search'                    => 'Поиск',
    'Reset'                     => 'Стереть',
//create.php
    'Create User'               => 'Создать пользователя',
    'Users'                     => 'Пользователи',
//index.php
    '-all-'                     => '-все-',
  //'Create User'               => 'Создать пользователя',
    'Role(s)'                   => 'Роли',
    'Actions'                   => 'Действия',
    'Are you sure to change status?'
                                => 'Ви уверены, что хотите изменить статус?',
//login.php
    'Admin login'               => 'Войти в админку',
    'Login'                     => 'Войти',
//update.php
    'Update user'               => 'Изменить пользователя',
  //'Users'                     => 'Пользователи',
  //'Update'                    => 'Обновить',
//view.php
  //'Users'                     => 'Пользователи',
  //'Update'                    => 'Обновить',
    'Delete'                    => 'Удалить',
    'Are you sure you want to delete this item?'
                                => 'Вы уверены, что хотите удалить эту запись?',

// views/_mail
//confirm.php
    'Dear'                      => 'Уважаемый',
    'You have been registered on our site'
                                => 'Вы зарегистрировались на нашем сайте',
    'Follow this link to confirm your registration:'
                                => 'Следуйте по этой ссылке для подтверждения регистрации:',
    "If you don't register on our site remove this mail."
                                => 'Если вы не регистрировались на нашем сайте, удалите это письмо.',

];
