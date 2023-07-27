<?
use \Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);
$component->SetResultCacheKeys(array("arResult"));
?>
<div class="FebSearch">
	<div class="Container">
		<form method="get" action="<?php echo $arParams["CATALOGUE_LINK"];?>">
			<p><input
			type="text"
			class="CatalogueSearchInput"
			name="q"
			value='<?php if(!\in_array($arParams["SEARCH_PARAM"],$arResult["vendor"])){
				echo htmlspecialchars($arParams["SEARCH_PARAM"]);
				} ?>'
			autocomplete="off"
			minlength="2"
			id="headerNumberSearch"
			placeholder='<?php echo Loc::getMessage("SEARCH_PLACEHOLDER");?>'
			data-mobilePlaceholderDefault="<?php echo Loc::getMessage("MOBILE_PLACEHOLDER");?>"
			data-mobilePlaceholderActive="<?php echo Loc::getMessage("MOBILE_VENDOR");?>"
			data-vendor='[<?foreach($arResult["manufacturer"] as $manufacturer):?>"<?=preg_replace("|'|",'`',$manufacturer);?>",<?endforeach;?>""]'
			/><label for="headerNumberSearch" data-conditions="<?php echo Loc::getMessage("SEARCH_CONDITION");?>"></label>
			</p>
			<p><input type="submit" class="CatalogueSearchSubmit1" value="<?php echo Loc::getMessage("SEARCH_BUTTON");?>" /></p>
		</form>
	</div>
	<div class="Container">
		<!---  TUT -->
		<form method="get" action="<?php echo $arParams["CATALOGUE_LINK"];?>?q=">
			<select name="vendor" class="vendor">
				<option value="">
					- <?php echo Loc::getMessage("SELECT_MANUFACTURER");?> -
				</option>
				<? if (sizeof($arResult["vendor"]) > 0) : ?>
					<? foreach ($arResult["vendor"] as $vendor) :?>
				<option value="<?php echo $vendor; ?>"
					<?php if($arParams["SEARCH_PARAM"]==$vendor):?>
						selected="selected"
					<?php endif;?>
					><?php echo $vendor;?></option>
					<? endforeach; ?>
				<?endif;?>
			</select>
			<input type="submit" class="CatalogueSearchSubmit" value="<?php echo Loc::getMessage("BUTTON_SHOW");?>" />
		</form>
	</div>
		<!---  TUT -->
			<?$APPLICATION->IncludeComponent(
				"bitrix:catalog.section.list",
				"top-line",
				Array(
					"ADD_SECTIONS_CHAIN" => "N",
					"CACHE_FILTER" => "N",
					"CACHE_GROUPS" => "Y",
					"CACHE_TIME" => "3600",
					"CACHE_TYPE" => "A",
					"COUNT_ELEMENTS" => "N",
					"COUNT_ELEMENTS_FILTER" => "CNT_ACTIVE",
					"FILTER_NAME" => "",
					"IBLOCK_ID" => "101",
					"IBLOCK_TYPE" => "element",
					"SECTION_CODE" => "",
					"SECTION_FIELDS" => array("NAME"),
					"SECTION_ID" => "",
					"SECTION_URL" =>"",
					"SECTION_USER_FIELDS" => array("",""),
					"SHOW_PARENT_NAME" => "Y",
					"TOP_DEPTH" => "2",
					"VIEW_MODE" => "LINE"
				),
			$component
			);?>
</div>