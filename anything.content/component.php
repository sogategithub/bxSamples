<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if($GLOBALS['APPLICATION']->GetShowIncludeAreas()){
	$this->IncludeComponentTemplate();
	}
elseif(!empty($arParams["ACTION_PARAM"])&&$this->request->get($arParams["ACTION_PARAM"])!==null){
	\BXClearCache(true,"/".SITE_ID.$this->getRelativePath());
	$this->IncludeComponentTemplate();
	}
else{
	$cacheID="";
	if($arParams["USER_CACHE"]==="Y"&&$GLOBALS["USER"]->isAuthorized()){
		$cacheID=$GLOBALS["USER"]->GetID();
		}

	if($this->StartResultCache($arParams['CACHE_TIME'],$cacheID)){
		$this->IncludeComponentTemplate();
		}
	}