<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
use \Bitrix\Main\Localization\Loc,
		\Bitrix\Main\Error,
		\Bitrix\Main\Errorable,
		\Bitrix\Main\ErrorCollection,
		\Bitrix\Main\Page\Asset,
		\Bitrix\Main\Engine\Contract\Controllerable,
		\Bitrix\Main\Engine\ActionFilter,
		\Chelnynet\Orders;

Loc::loadMessages(__FILE__);
class elementMassCross extends CBitrixComponent implements Controllerable, Errorable{
	protected $error;
	public $HlBlockClass="\Chelnynet\Catalogue\CatalogueElementCrossTable";

	public function onPrepareComponentParams($arParams){
		$this->error=new ErrorCollection();
		$this->csvHeaders=[
			"UF_PRODUCT"=>Loc::getMessage("ELEMENT_MASS_CROSS_HEADERS_PRODUCT_ID"),
			"PRODUCT_NAME"=>Loc::getMessage("ELEMENT_MASS_CROSS_HEADERS_PRODUCT_NAME"),
			"UF_SORT" => Loc::getMessage("ELEMENT_MASS_CROSS_HEADERS_SORT"),
			"UF_VENDOR" => Loc::getMessage("ELEMENT_MASS_CROSS_HEADERS_VENDOR"),
			"UF_CODE" => Loc::getMessage("ELEMENT_MASS_CROSS_HEADERS_CODE"),
			"UF_OEM" => Loc::getMessage("ELEMENT_MASS_CROSS_HEADERS_OEM")
			];
		return $arParams;
		}

	protected function listKeysSignedParameters(){
		return [];
		}

	public function configureActions(){}

	public function getErrors(): array{
		return $this->error->toArray();
		}

	public function getErrorByCode($code): Error{
		return $this->error->getErrorByCode($code);
		}

	public function csvFromArray(array $rows):string{
		$result="";
		foreach($this->csvHeaders as $header){
			$result.='"'.$header.'";';
			}
		$result.="\n";
		foreach($rows as $row){
			foreach($this->csvHeaders as $key=>$header){
				$result.='"'.($row[$key]??"").'";';
				}
			$result.="\n";
			}
		return $result;
		}

	public function emptyProducts(array $products):array{
		$existsProducts=array_column($products,"UF_PRODUCT");
		return (new $this->HlBlockClass)->getEmptyProducts($existsProducts);
		}

	public function getAction(){
		if(!\CSite::inGroup(\rostarRoles::getProductManagerGroups())){
			$this->error[]=new Error(
				Loc::getMessage('ELEMENT_MASS_CROSS_DENIED')
				);
			return;
			}
		$rows=(new $this->HlBlockClass)->getFullProducts();
		// add products without crosses
		$emptyProducts=$this->emptyProducts($rows);
		$rows=array_merge($rows,$emptyProducts);
		$rows=$this->csvFromArray($rows);
		$rows=$GLOBALS["APPLICATION"]->ConvertCharset($rows,"utf-8","windows-1251");
		$rows=base64_encode($rows);
		return $rows;
		}

	public function prepareText($e){
		$e=trim($e);
		$e=preg_replace("/(\"|')/","",$e);
		$e=$GLOBALS["APPLICATION"]->ConvertCharset($e,"windows-1251","utf-8");
		return $e;
		}

	public function getUpdateArray(\CCSVData $csvFile, array $processingIDs):array{
		// get highload block fiels
		$headers=array_keys($this->csvHeaders);
		$update=[];
		$count=0;
		while($res=$csvFile->Fetch()){
			// ignoring ids not iblock elements
			if(!in_array($res[0],$processingIDs)){
				continue;
				}
			$colCount=0;
			foreach($headers as $header){
				$update[$count][$header]=$this->prepareText($res[$colCount]);
				$colCount++;
				}
			unset($update[$count]["PRODUCT_NAME"]);
			if($update[$count]["UF_OEM"]!=1){
				$update[$count]["UF_OEM"]=0;
				}
			// ||empty($update[$count]["UF_CODE"])
			if(empty($update[$count]["UF_VENDOR"])){
				unset($update[$count]);
				}
			$count++;
			}
		return $update;
		}

	public function putAction(){
		// access
		if(!\CSite::inGroup(\rostarRoles::getProductManagerGroups())){
			$this->error[]=new Error(
				Loc::getMessage('ELEMENT_MASS_CROSS_DENIED')
				);
			return;
			}

		$class=new $this->HlBlockClass;
		//elements list in iblock
		$processingIDs=$class->getIblockID();
		if(sizeof($processingIDs)===0){
			$this->error[]=new Error(
				Loc::getMessage('ELEMENT_MASS_CROSS_HAVE_NOT_ACTIVE_PRODUCTS')
				);
			return;
			}

		// file exists
		$file=$this->request->getFile("file");
		if(!isset($file["tmp_name"])||empty($file["tmp_name"])){
			$this->error[]=new Error(
				Loc::getMessage('ELEMENT_MASS_CROSS_FILE_NOT_LOADED')
				);
			return;
			}

		$csvFile=new \CCSVData('R',true);
		$csvFile->LoadFile($file["tmp_name"]);
		$csvFile->SetDelimiter(';');
		$update=$this->getUpdateArray($csvFile,$processingIDs);
		$csvFile->CloseFile();
		unlink($file["tmp_name"]);

		// no update elements
		if(sizeof($update)===0){
			$this->error[]=new Error(
				Loc::getMessage('ELEMENT_MASS_CROSS_NO_ROWS')
				);
			return;
			}

		$class->truncate();
		foreach($update as $element){
			$class::add($element);
			}
		$class->updateSearchIndex($update);
		return [
			"rows_processed"=>sizeof($update)
			];
		}

	public function executeComponent(){
		if(!\CSite::inGroup(\rostarRoles::getProductManagerGroups())){
			$this->error[]=new Error(
				Loc::getMessage('ELEMENT_MASS_CROSS_DENIED')
				);
			}
		$this->arResult['ERRORS']=$this->getErrors();
		\Bitrix\Main\UI\Extension::load([
			"jquery",
			"ui.alerts",
			"ui.buttons",
			"ui.dialogs.messagebox",
			"ui.notification"
			]);
		$this->includeComponentTemplate();
		}
	}