<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
use \Bitrix\Main\Localization\Loc;
?>
<?php if(is_array($arResult)):?>
    <table border="1">
        <thead>
        <tr>
            <th>Фамилия</th>
            <th>Имя</th>
            <th>Уровень доступа</th>
            <th>Дата и время последнего входа в систему</th>
            <th>Комментарий</th>
            <th>Объект</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach($arResult as $row):?>
            <tr>
                <td><?php echo $row['LAST_NAME'];?></td>
                <td><?php echo $row['NAME'];?></td>
                <td><?php echo $row['UF_RIGHTS'];?></td>
                <td><?php echo $row['LAST_LOGIN'];?></td>
                <td><?php echo $row['UF_COMMENT'];?><br /><?php echo $row['OBJECT_COMMENT'];?></td>
                <td><?php echo $row['OBJECT_NAME'];?></td>
            </tr>
        <?php endforeach;?>
        </tbody>
    </table>
<?php endif;?>



<?php /*echo"<pre>".print_r($arParams,true)."</pre>";*/ ?>
<?php /*echo"<pre>".print_r($arResult,true)."</pre>";*/ ?>