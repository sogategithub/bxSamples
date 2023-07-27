<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use \Bitrix\Main\Localization\Loc;
if(\in_array($arParams["SEARCH_PARAM"],$arResult["vendor"]))
{
	$title=\htmlspecialcharsbx($arParams["SEARCH_PARAM"]);
	$GLOBALS["APPLICATION"]->AddChainItem(
		Loc::getMessage("ANYTHING_CONTENT_TOP_SEARCH_TITLE",[
			"#NAME#"=>$title
			])
		);
	$GLOBALS["APPLICATION"]->SetTitle($title);
}