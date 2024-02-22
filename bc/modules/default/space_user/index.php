<?php

$ROOTDIR = $_SERVER['DOCUMENT_ROOT'];
require_once $ROOTDIR . "/vars.inc.php";
require_once $ROOTDIR . "/bc/connect_io.php";
require_once $ROOTDIR . "/bc/modules/default/function.inc.php";

global $db, $AUTH_USER_ID;

Authorize();

if (!getIP('office') && !permission('developer')) die();

$spaceOccupiedUser = json_decode(file_get_contents(__DIR__ . '/space_occupied_user_cache.json'), 1);
$totalMemory = mbOrGb(array_sum($spaceOccupiedUser));

$catalogs = array_reduce($db->get_results(
    "SELECT 
        `Catalogue_ID`,
        `Domain`,
        `Catalogue_Name`,
        `login`,
        `Checked`
    FROM
        Catalogue",
    ARRAY_A
), function($carry, $catalog) {
    $carry[$catalog['login']] = $catalog;
    return $carry;
}, []);

$data = [];
array_walk($spaceOccupiedUser, function($value, $login) use ($catalogs, &$data) {
    $data[] = array_merge(($catalogs[$login] ?: []), ['memory' => mbOrGb($value), 'login' => $login]);
});

unset($catalogs, $spaceOccupiedUser);


echo k_renderTemplate(__DIR__ . '/template/statistics_board.html', ['data' => $data, 'totalMemory' => $totalMemory]);

function mbOrGb($value) {
    if ($value >= 1024) return round($value / 1024) . 'Gb';
    return $value . 'Mb';
}