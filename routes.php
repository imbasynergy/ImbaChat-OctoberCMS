<?php

Route::get('imbachat/api/v1/users/{ids}', 'ImbaSynergy\imbachatwidget\Controllers\apiChat@getuser');
Route::get('imbachat/jwt', 'ImbaSynergy\imbachatwidget\Components\ImbaChat@getJWT');

?>