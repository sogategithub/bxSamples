<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$component=$this->__component;
if (is_object($component))
{
	$component->arResult['vendor']=(new \Chelnynet\Catalogue\CatalogueElementCrossTable())->getOemList();
	$component->SetResultCacheKeys(["vendor"]);
}
?>