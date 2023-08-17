<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("testdispatcher");
?>
<?php $APPLICATION->IncludeComponent(
	"chelnynet.testdispatcher:list",
	"",
	Array(
		"CACHE_TIME" => "3600",
		"CACHE_TYPE" => "A"
	)
);?>
<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>