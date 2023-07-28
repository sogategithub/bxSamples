<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
use \Bitrix\Main\Localization\Loc;
$blockID=$this->randString();
?>
<?php if($arResult['ERRORS']):?>
	<div class="ui-alert ui-alert-danger">
		<span class="ui-alert-message"><?php echo implode('<br />',$arResult['ERRORS'])?></span>
	</div>
<?php else:?>
	<div class="adm-detail-content-wrap">
			<div class="adm-detail-content">
				<?php echo Loc::getMessage("ELEMENT_MASS_CROSS_INFO");?>
				<div
					class="element_mass_cross_buttons"
					data-params="<?php echo $this->getComponent()->getSignedParameters();?>"
					data-component="<?php echo $component->getName();?>"
					data-error="<?php echo Loc::getMessage("ELEMENT_MASS_CROSS_ERROR");?>"
					data-undefinedError="<?php echo Loc::getMessage("ELEMENT_MASS_CROSS_UNDEFINED_ERROR");?>"
					data-done="<?php echo Loc::getMessage("ELEMENT_MASS_CROSS_DONE");?>"
					data-message="<?php echo Loc::getMessage("ELEMENT_MASS_CROSS_MESSAGE");?>"
					data-closebutton="<?php echo Loc::getMessage("ELEMENT_MASS_CROSS_CLOSE_BUTTON");?>"
					>
					<a class="ui-btn ui-btn-primary"><?php echo Loc::getMessage("ELEMENT_MASS_CROSS_GET");?></a>
					<a class="ui-btn ui-btn-danger"><?php echo Loc::getMessage("ELEMENT_MASS_CROSS_PUT");?></a>
					<input type="file" />
				</div>
				<br />
			</div>
	</div>
<?php endif;?>