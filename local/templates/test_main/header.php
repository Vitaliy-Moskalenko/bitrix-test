<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Page\Asset;
CModule::IncludeModule('iblock');

Bitrix\Main\Page\Asset::getInstance()->addCss('/bitrix/css/main/grid/webform-button.css');

?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="format-detection" content="telephone=no" />
  <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
  <link type="image/x-icon" rel="shortcut icon" href="/favicon.ico">
  <title><?$APPLICATION->ShowTitle()?></title>
    <?$APPLICATION->ShowHead();?>
</head>

<body class="body">
<div class="header-panel" style="position: absolute"><? $APPLICATION->ShowPanel(); ?></div>
  <div class="wrapper">
    <div class="header<?=$mainPage ? "-index" : ""?>">
      <div class="wrap">
        <div class="header__top">
        </div>
        <div class="header__bottom">
		  <div class="header__logo">
			<img src="<?=SITE_TEMPLATE_PATH?>/images/Logo.jpg" alt="">
		  </div>
        </div>
      </div>
    </div>
    <main class="container">