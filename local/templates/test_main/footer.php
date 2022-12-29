<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main\Page\Asset;
?>
</main>

<footer class="footer">
    <div class="wrap">
        <div class="footer__left">
            <div class="footer__agreement">
                &copy; <?=date("Y")." Виталий Москаленко<br>Санкт-Петербург"?>
            </div>
        </div>
    </div>
</footer>


<? Asset::getInstance()->addJs(SITE_TEMPLATE_PATH.'/js/jquery-3.2.1.min.js');?>
<? Asset::getInstance()->addJs(SITE_TEMPLATE_PATH.'/js/jquery.maskedinput.min.js');?>
<? Asset::getInstance()->addJs(SITE_TEMPLATE_PATH.'/js/libs.js');?>
<? Asset::getInstance()->addJs(SITE_TEMPLATE_PATH.'/js/main.js');?>

</body>
</html>