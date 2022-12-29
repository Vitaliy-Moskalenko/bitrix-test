<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

$APPLICATION->SetTitle("Форма обратной связи");

use Bitrix\Highloadblock\HighloadBlockTable as HLBT;
use \Bitrix\Iblock\PropertyEnumerationTable;
use Bitrix\Main\Grid\Options as GridOptions;
use Bitrix\Main\UI\PageNavigation;

CModule::IncludeModule("iblock");
CModule::IncludeModule('highloadblock');

// функция получения экземпляра класса:
function GetEntityDataClass($HlBlockId) {
    if (empty($HlBlockId) || $HlBlockId < 1)
		return false;
    
    $hlblock = HLBT::getById($HlBlockId)->fetch();
    $entity  = HLBT::compileEntity($hlblock);
    $entity_data_class = $entity->getDataClass();
    return $entity_data_class;
}

$list_id = 'feedback';
$pageSize = ($nav_params['nPageSize']) ? $nav_params['nPageSize'] : 30;

$grid_options = new GridOptions($list_id);  
$sort = $grid_options->GetSorting(['sort' => ['ID' => 'DESC'], 'vars' => ['by' => 'by', 'order' => 'order']]);  // exit(var_dump( $sort ));
$nav_params = $grid_options->GetNavParams();

$nav = new PageNavigation($list_id);
$nav->allowAllRecords(true)
	->setPageSize($pageSize)
	->initFromUri();
	
if ($nav->allRecordsShown()) {
	$nav_params = false;
} else {
	$nav_params['iNumPage'] = $nav->getCurrentPage();
}
?>

<section class="article">
    <div class="wrap">	
		<div id="request">
			<!-- При необходимости форма может быть реализована как модальное окно -->
			<form class="modal__block" method="post">
				<div class="modal__title">Форма обратной связи.</div>
				<div class="modal__sub-title">Пожалуйста, заполните все необходимые поля</div>
				<input type="hidden" name="event" value="request">
				<div class="wrap__input">
					<sup>*</sup>
					<input type="text" name="name" class="input" placeholder="Ф.И.О." required>
				</div>
				<div class="wrap__input">
					<sup>*</sup>
					<input type="email" name="email" class="input" placeholder="E-Mail" required>
				</div>
				<div class="wrap__input">
					<input type="text" name="phone" class="input" placeholder="Телефон">
				</div>
				<div class="wrap__input">
					<sup>*</sup>
					<textarea class="textarea" name="message" row="3"  placeholder="Ваш вопрос" required></textarea>
				</div>
				<div class="allert-required">
					<sup>*</sup> - обязательные поля для заполнения
				</div>
				<div class="modal__footer">
					<button type="submit" class="btn">Отправить</button>
				</div>
			</form>
			<form class="success">
				<span class="modal__close">×</span>
				<div class="modal__sub-title"></div>
			</form>
		</div>	
		
    </div>
</section>

<section class="article grid">
    <div class="wrap">
		<h2 class="grid__title">Запросы</h2>
<?
$columns = [];
$columns[] = ['id' => 'ID',              'name' => 'ID',      'sort' => 'ID',           'default' => true];
$columns[] = ['id' => 'UF_FIO',          'name' => 'Ф.И.O.',  'sort' => 'UF_FIO',       'default' => true];
$columns[] = ['id' => 'UF_EMAIL',        'name' => 'E-Mail',  'sort' => 'UF_EMAIL',        'default' => true];
$columns[] = ['id' => 'UF_PHONE',        'name' => 'Phone',   'sort' => 'UF_PHONE',        'default' => true];
$columns[] = ['id' => 'UF_DATE_CREATED', 'name' => 'Создано', 'sort' => 'UF_DATE_CREATED', 'default' => true];

$entity_data_class = GetEntityDataClass(FEEDBACK_HL_BLOCK);
$res = $entity_data_class::getList(array('select' => array('*'), 'order' => $sort['sort']));

$nav->setRecordCount($entity_data_class::getCount());
while($row = $res->fetch()) {	
	$list[] = [
		'data' => [
			"ID"              => $row['ID'],
			"UF_FIO"          => $row['UF_FIO'],
			"UF_EMAIL"        => $row['UF_EMAIL'],
			"UF_PHONE"        => $row['UF_PHONE'],
			"UF_DATE_CREATED" => $row['UF_DATE_CREATED'],
		],
	];
}

$APPLICATION->IncludeComponent('bitrix:main.ui.grid', '', [
	'GRID_ID' => $list_id,
	'COLUMNS' => $columns,
	'ROWS'    => $list,
	'SHOW_ROW_CHECKBOXES' => false,
	'NAV_OBJECT' => $nav,
	'AJAX_MODE' => 'Y',
	'AJAX_ID' => \CAjax::getComponentID('bitrix:main.ui.grid', '.default', ''),
	'PAGE_SIZES' =>  [
		['FIO' => '20', 'VALUE' => '20'],
		['FIO' => '50', 'VALUE' => '50'],
		['FIO' => '100', 'VALUE' => '100']
	],
	'AJAX_OPTION_JUMP'          => 'N',
	'SHOW_CHECK_ALL_CHECKBOXES' => true,
	'SHOW_ROW_ACTIONS_MENU'     => true,
	'SHOW_GRID_SETTINGS_MENU'   => true,
	'SHOW_NAVIGATION_PANEL'     => true,
	'SHOW_PAGINATION'           => true,
	'SHOW_SELECTED_COUNTER'     => true,
	'SHOW_TOTAL_COUNTER'        => true,
	'SHOW_PAGESIZE'             => true,
	'SHOW_ACTION_PANEL'         => true,
	'ALLOW_COLUMNS_SORT'        => true,
	'ALLOW_COLUMNS_RESIZE'      => true,
	'ALLOW_HORIZONTAL_SCROLL'   => true,
	'ALLOW_SORT'                => true,
	'ALLOW_PIN_HEADER'          => true,
	'AJAX_OPTION_HISTORY'       => 'N'
]);

?>
	</div>
</section>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");