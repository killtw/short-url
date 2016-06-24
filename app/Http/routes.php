<?php

$app->post('store', ['as' => 'store', 'uses' => 'RedirectController@store']);
$app->get('{hash}+', ['as' => 'decode', 'uses' => 'RedirectController@decode']);
$app->get('{hash}', ['as' => 'redirect', 'uses' => 'RedirectController@redirect']);
