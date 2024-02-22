<?php

/* $Id: ru.lang.php 7302 2012-06-25 21:12:35Z alive $ */

if (!class_exists("nc_System")) die("Unable to load file.");

if ($nc_core->NC_UNICODE) {
    require_once "ru_utf8.lang.php";
} else {
    require_once "ru_cp1251.lang.php";
}