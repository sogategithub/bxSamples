<?php
namespace Chelnynet\TestDispatcher;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\DatetimeField;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\ORM\Fields\TextField;
use Bitrix\Main\ORM\Fields\Validators\LengthValidator;
use Bitrix\Main\Type\DateTime;

Loc::loadMessages(__FILE__);

class TestobjectTable extends DataManager{
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'ch_testobjects';
	}

	/**
	 * Returns entity map definition.
	 *
	 * @return array
	 */
	public static function getMap()
	{
		return [
			new IntegerField(
				'ID',
				[
					'primary' => true,
					'title' => Loc::getMessage('TESTOBJECTS_ENTITY_ID_FIELD')
				]
			),
			new DatetimeField(
				'UF_CREATED',
				[
					'default' => function()
					{
						return new DateTime();
					},
					'title' => Loc::getMessage('TESTOBJECTS_ENTITY_UF_CREATED_FIELD')
				]
			),
			new StringField(
				'UF_NAME',
				[
					'required' => true,
					'validation' => [__CLASS__, 'validateUfName'],
					'title' => Loc::getMessage('TESTOBJECTS_ENTITY_UF_NAME_FIELD')
				]
			),
			new TextField(
				'UF_ADDRESS',
				[
					'required' => true,
					'title' => Loc::getMessage('TESTOBJECTS_ENTITY_UF_ADDRESS_FIELD')
				]
			),
			new TextField(
				'UF_COMMENT',
				[
					'required' => true,
					'title' => Loc::getMessage('TESTOBJECTS_ENTITY_UF_COMMENT_FIELD')
				]
			),
		];
	}

	/**
	 * Returns validators for UF_NAME field.
	 *
	 * @return array
	 */
	public static function validateUfName()
	{
		return [
			new LengthValidator(null, 255),
		];
	}
}
?>