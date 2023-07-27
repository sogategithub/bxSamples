<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentParameters=[
	"GROUPS"=>[],
	"PARAMETERS" =>[
		"ACTION_PARAM"=>[
			"PARENT"=>"BASE",
			"NAME"=>getMessage("CHELNYNET_ANYTHING_CONTENT_ACTION_PARAM"),
			"TYPE"=>"STRING",
			"REFRESH"=>"N",
			"MULTIPLE"=>"N",
			"DEFAULT"=>"",
			"SORT"=>"100",
			],
		"USER_CACHE"=>[
			"PARENT"=>"CACHE_SETTINGS",
			"NAME"=>getMessage("CHELNYNET_ANYTHING_CONTENT_USER_CACHE"),
			"TYPE"=>"CHECKBOX",
			"REFRESH"=>"N",
			"MULTIPLE"=>"N",
			"DEFAULT"=>"N",
			"SORT"=>"100",
			],
		"CACHE_TIME"=>[
			"DEFAULT"=>3600
			],
		],
	];
?>