<?php
function amoCrm($param, $type = '') {
	global $setting;
	
	if (!$setting['actoken']) return;


	if ($setting['actimestamp'] <= time()) {
		$dataNewToken = [
			'client_id' =>  $setting['acclientid'],
			'client_secret' => $setting['acclientsecret'],
			'grant_type' => 'refresh_token',
			'refresh_token' => $setting['acreftoken'],
			'redirect_uri' => ($_SERVER['HTTPS'] == 'on' ? 'https' : 'http')."://{$_SERVER['HTTP_HOST']}/"
		];
		$link = "https://{$setting['acsubdomen']}.amocrm.ru/oauth2/access_token";
		$response = curlPostAmoCrm($link, json_encode($dataNewToken));
		if ($response['access_token']) {
			$setting['actoken'] = $response['access_token']; //Access токен
			$setting['acreftoken'] = $response['refresh_token']; //Refresh токен
			$setting['actime'] = date("Y-m-d") .' по '. date("Y-m-d", time() + $response['expires_in']);
			$setting['actimestamp'] = time() + $response['expires_in']; //Время жизни токена
			setSettings($setting);
		}
	}

	switch ($type) {
		case 'mail':
			$tags = "Письмо с сайта";
			break;
		case 'cart':
		default:
			$tags = "Заказ с сайта";
		break;
	}
		
	$unsortedData = [
		[
		"request_id" => "123",
		"source_name" => "Форма с сайта",
		"source_uid" => uniqid('', true),
		"_embedded" => []
		]
	];

	//Добовляем тег
	if ($tags) {
		$unsortedData[0]['_embedded']['leads'] = [[
			"name" => $param['TITLE'],
			"_embedded" => [
				'tags' => [["name" => $tags]]
			]
		]];
		
	}
	// Добовляем контакт
	if ($param['PHONE'] || $param['NAME'] || $param['EMAIL']) {
		$unsortedData[0]['_embedded']['contacts'] = [
			[
				"first_name" => $param['NAME'],
				"custom_fields_values" => [
					[
						"field_code" => 'PHONE',
						"values" => [
							["value" => $param['PHONE']]
						]
					],
					[
						"field_code" => 'EMAIL',
						"values" => [
							["value" => $param['EMAIL']]
						]
					]
				]
			]
		]; 
	}

	// metadata 
	$unsortedData[0]['metadata'] = [
		"category" => "forms",
        "form_id" => 333,
        "form_name" => $param['TITLE'],
        "form_sent_at" => time (),
        "ip" => getIP(''),
        "form_page" =>  ($_SERVER['HTTPS'] == 'on' ? 'https' : 'http')."://{$_SERVER['HTTP_HOST']}/",
        "referer" => ($_SERVER['HTTPS'] == 'on' ? 'https' : 'http')."://{$_SERVER['HTTP_HOST']}/"
	];

	$unsorted = curlPostAmoCrm("https://{$setting['acsubdomen']}.amocrm.ru/api/v4/leads/unsorted/forms", json_encode($unsortedData, JSON_UNESCAPED_UNICODE));
	$idLeads = $unsorted['_embedded']['unsorted'][0]['_embedded']['leads']['0']['id'];

	if($param['COMMENTS'] && $idLeads) {
		$notesData[] = [
			"entity_id" => $idLeads,
			"note_type" => "service_message",
			"params" =>[
					"service" => "Коментарий",
					"text" => $param['COMMENTS']
			] 
		];
		$unsorted2 = curlPostAmoCrm("https://{$setting['acsubdomen']}.amocrm.ru/api/v4/leads/notes",json_encode($notesData, JSON_UNESCAPED_UNICODE));
	}
}
function curlGetAmoCrm($url) {
	global $setting;

	$aromCrmKey = $setting['actoken'];
	$headers = [
		'Authorization: Bearer '.$aromCrmKey,
		'Content-Type: application/json'
	];

	$ch=curl_init();
	curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch,CURLOPT_USERAGENT,'amoCRM-oAuth-client/1.0');
	curl_setopt($ch,CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch,CURLOPT_HEADER, false);
	curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, 1);
	curl_setopt($ch,CURLOPT_SSL_VERIFYHOST, 2);
	curl_setopt($ch,CURLOPT_CUSTOMREQUEST, 'GET');
	$reply=curl_exec($ch);
	curl_close($ch);

	return json_decode($reply,1);
}

function curlPostAmoCrm($url, $data) {
	global $setting;

	$aromCrmKey = $setting['actoken'];
	$headers = [
		'Authorization: Bearer '.$aromCrmKey,
		'Content-Type: application/json'
	];

	$data = str_replace("\n",'',str_replace("\r",'',$data));
	$ch=curl_init();
	curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch,CURLOPT_USERAGENT,'amoCRM-oAuth-client/1.0');
	curl_setopt($ch,CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch,CURLOPT_HEADER, false);
	curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, 1);
	curl_setopt($ch,CURLOPT_SSL_VERIFYHOST, 2);
	curl_setopt($ch,CURLOPT_CUSTOMREQUEST, 'POST');
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	$reply=curl_exec($ch);
	curl_close($ch);

	return json_decode($reply,1);
}
