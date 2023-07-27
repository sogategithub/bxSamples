<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div itemscope="" itemtype="http://schema.org/Organization">
	<span itemprop="name"><?php echo $arParams["NAME"];?></span><br />
	<span itemprop="address"><?php echo htmlspecialcharsBack($arParams["ADRESS"]);?></span><br />
	E-mail: <a href="mailto:<?php echo $arParams["EMAIL"];?>" rel="nofollow"><strong itemprop="email"><?php echo $arParams["EMAIL"];?></strong></a><br />
	Горячая линия: <a href="tel:+7<?php echo $arParams["HOTLINE"];?>" rel="nofollow"><strong itemprop="telephone">8-<?php echo $arParams["HOTLINE"];?></strong></a>
</div>