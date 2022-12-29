<?php
require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

use Bitrix\Main\Context;
use Bitrix\Main\Localization\Loc;
use Bitrix\Highloadblock\HighloadBlockTable as HLBT; // подключаем пространство имен класса HighloadBlockTable и даём ему псевдоним HLBT для удобной работы

Loc::loadLanguageFile(__FILE__);

$request = Context::getCurrent()->getRequest();

$successMessage = Loc::GetMessage("SUCCESS_MESSAGE");

$success  = true;
$messages = array();

// функция получения экземпляра класса:
function GetEntityDataClass($HlBlockId) {
    if (empty($HlBlockId) || $HlBlockId < 1)
		return false;
    
    $hlblock = HLBT::getById($HlBlockId)->fetch();
    $entity  = HLBT::compileEntity($hlblock);
    $entity_data_class = $entity->getDataClass();
    return $entity_data_class;
}

if ($request->isPost()) {
    // вид формы
    $event = $request->getPost('event');

    // параметры из формы
    $name    = trim(filter_var($request->getPost('name'), FILTER_SANITIZE_STRING));
    $phone   = trim(filter_var($request->getPost('phone'), FILTER_SANITIZE_STRING));
    $email   = trim(filter_var($request->getPost('email'), FILTER_SANITIZE_STRING));
    $message = trim(filter_var($request->getPost('message'), FILTER_SANITIZE_STRING));

    // адрес куда будет отпралено сообщение
    $email_to = COption::GetOptionString('main', 'email_from');

    // проверяем поля, которые не должны быть пустыми
    if (!preg_match("#^([ёЁ\sA-zА-я -]*)$#ui", $name)) {
        $success = false;
        $messages[] = Loc::GetMessage("NAME_ERROR");
    }

    if (($event != 'request-demo') && !preg_match("/^[0-9\-+ )(]*$/", $phone)) {
        $success = false;
        $messages[] = Loc::GetMessage("PHONE_ERROR");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $success = false;
        $messages[] = Loc::GetMessage("EMAIL_ERROR");
    }

	// используем highload-block
	if($success && CModule::IncludeModule('highloadblock')) {
		$entity_data_class = GetEntityDataClass(FEEDBACK_HL_BLOCK);
		if($entity_data_class::add(array(
			'UF_FIO'      => $name,
			'UF_EMAIL'    => $email,
			'UF_PHONE'    => $phone,
			'UF_QUESTION' => $message,
			'UF_DATE_CREATED' => date('d.m.Y')
		))) {
				Bitrix\Main\Mail\Event::send(array(
					"EVENT_NAME" => "REQUEST_FORM_PRICE",
					"LID" => SITE_ID,
					"C_FIELDS" => [
						'AUTHOR_NAME'  => $name,
						'AUTHOR_PHONE' => $phone,
						'AUTHOR_EMAIL' => $email,
						'EMAIL_TO'     => $email_to,
						"TEXT"=>$message,
						"PAGE"=>$page,
					],
				));
				$success = true;
				$messages[] = $successMessage;
			} else {
				$success = false;
				$messages[] = Loc::GetMessage("SEND_ERROR");
			}		
	}

	// используем инфоблок
    /* if ($success && CModule::IncludeModule("iblock")) {
        $arElement = new CIBlockElement;
        switch ($event) {   
            case 'request':
				if($arElement->Add([
                    "IBLOCK_ID" => IB_REQUEST,
                    "NAME" => $name,
                    "PROPERTY_VALUES" => [
                        "FIO"          => $name,
						"PHONE_NUMBER" => $phone,
                        "MESSAGE"      => $message,                        
                        "EMAIL"        => $email,
                    ]
                ])) {
                    Bitrix\Main\Mail\Event::send(array(
                        "EVENT_NAME" => "REQUEST_FORM_PRICE",
                        "LID" => SITE_ID,
                        "C_FIELDS" => [
                            'AUTHOR_NAME'  => $name,
                            'AUTHOR_PHONE' => $phone,
                            'AUTHOR_EMAIL' => $email,
                            'EMAIL_TO'     => $email_to,
                            "TEXT"=>$message,
                            "PAGE"=>$page,
                        ],
                    ));
                    $success = true;
                    $messages[] = $successMessage;
                } else {
                    $success = false;
                    $messages[] = Loc::GetMessage("SEND_ERROR");
                }
                break;
            case 'request-help':
                if($arElement->Add([
                    "IBLOCK_ID" => IB_REQUEST_HELP,
                    "NAME" => $name,
                    "PROPERTY_VALUES" => [
                        "PHONE_NUMBER"=>$phone,
                        "MESSAGE"=>$message,
                        "PAGE"=>$page,
                        "EMAIL"=>$email,
                        "INSTITUTION"=>$institution,
                        "INTERESTED"=>$interested,
                    ]
                ])) {
                    Bitrix\Main\Mail\Event::send(array(
                        "EVENT_NAME" => "REQUEST_FORM",
                        "LID" => SITE_ID,
                        "C_FIELDS" => [
                            'AUTHOR_NAME' => $name,
                            'AUTHOR_PHONE' => $phone,
                            'AUTHOR_EMAIL' => $email,
                            'EMAIL_TO' => $email_to,
                            "TEXT"=>$message,
                            "PAGE"=>$page,
                            "INSTITUTION"=>$institution,
                            "INTERESTED"=>$interested,
                        ],
                    ));
                    $success = true;
                    $messages[] = $successMessage;
                } else {
                    $success = false;
                    $messages[] = Loc::GetMessage("SEND_ERROR");
                }
                break;
        }
    } */
} else { // isPost
    $success = false;
    $messages[] = Loc::GetMessage("SEND_ERROR");
}

$response = array(
    'success'  => $success,
    'messages' => $messages
);

echo json_encode($response);