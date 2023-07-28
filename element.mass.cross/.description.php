<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
use \Bitrix\Main\Localization\Loc;
$arComponentDescription = array(
	"NAME" => Loc::getMessage("ELEMENT_MASS_CROSS_COMPONENT"),
	"ICON" => "/images/icon.gif",
	"SORT" => 120,
	"CACHE_PATH" => "Y",
	"PATH" =>[
		"ID" => "chelnynet",
		"CHILD" =>[
			"ID" => "rkatalog"
			],
		],
	);
?>