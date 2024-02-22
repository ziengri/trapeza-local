<?php

$PATH_TEMPLATE = str_replace($_SERVER['DOCUMENT_ROOT'], '', __DIR__) . '/templates/';

$templates = [
    [
        'title' => 'Блок выравнивание по центру',
        'description' => 'Выравнивает внутриние блоки по центру',
        'url' => $PATH_TEMPLATE . 'one_line_div.htm'
    ],
    [
        'title' => 'Фото с текстом',
        'description' => '',
        'url' => $PATH_TEMPLATE . 'photo_bottom_text.htm'
    ],
    [
        'title' => 'Текст под спойлером',
        'description' => '',
        'url' => $PATH_TEMPLATE . 'spoiler_text.htm'
    ]
];

echo json_encode($templates);