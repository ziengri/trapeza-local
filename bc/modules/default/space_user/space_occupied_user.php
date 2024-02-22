<?php

$SEARCH_DIR = '/var/www/krza/data/www/krza.ru/a/';

$spaceOccupiedUser = [];
foreach (glob($SEARCH_DIR . '*', GLOB_ONLYDIR) as $userFolder) {
    $login = str_replace($SEARCH_DIR, '', $userFolder);
    $spaceOccupiedUser[$login] = (int) array_shift(explode('	', exec("du -sm {$userFolder}")));
}
arsort($spaceOccupiedUser);

file_put_contents(__DIR__ . '/space_occupied_user_cache.json', json_encode($spaceOccupiedUser));

$SEARCH_DIR = '/var/www/krza/data/www/krza.ru/b/';

$spaceOccupiedUserB = [];
foreach (glob($SEARCH_DIR . '*', GLOB_ONLYDIR) as $userFolder) {
    $login = str_replace($SEARCH_DIR, '', $userFolder);
    $spaceOccupiedUserB[$login] = (int) array_shift(explode('	', exec("du -sm {$userFolder}")));
}
arsort($spaceOccupiedUserB);

file_put_contents(__DIR__ . '/space_occupied_user_cache_b.json', json_encode($spaceOccupiedUserB));