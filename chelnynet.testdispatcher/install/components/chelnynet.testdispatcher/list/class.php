<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
use \Bitrix\Main\Localization\Loc,
		\Bitrix\Main\Error,
		\Bitrix\Main\Errorable,
		\Bitrix\Main\ErrorCollection,
		\Bitrix\Main\Engine\Contract\Controllerable,
		\Chelnynet\TestDispatcher;

Loc::loadMessages(__FILE__);

if (!\Bitrix\Main\Loader::includeModule('chelnynet.testdispatcher')) {
	return;
	}

class chelnynetTestDispatcher extends CBitrixComponent implements Controllerable, Errorable{
	protected $error;

	public function onPrepareComponentParams($arParams){
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

	private function getList():self{
		$this->arResult=TestDispatcher\TestdispatcherTable::getList([
			'select'=>[
				'*',
				'NAME'=>'USER.NAME',
				'LAST_NAME'=>'USER.LAST_NAME',
				'LAST_LOGIN'=>'USER.LAST_LOGIN',
				'OBJECT_NAME'=>'OBJECT.UF_NAME',
				'OBJECT_COMMENT'=>'OBJECT.UF_COMMENT',
				],
			'runtime'=>[
				'OBJECT_LINK'=>[
					'data_type'=>'\Chelnynet\TestDispatcher\TestdisobTable',
					'reference'=>[
						'this.UF_USER'=>'ref.UF_USER'
						],
					'join_type'=>'left'
					],
				'OBJECT'=>[
					'data_type'=>'\Chelnynet\TestDispatcher\TestobjectTable',
					'reference'=>[
						'this.OBJECT_LINK.UF_OBJECT'=>'ref.ID'
						],
					'join_type'=>'left'
					],
				],
			'cache'=>[
				'ttl' =>$this->arParams['CACHE_TIME'],
				'cache_joins'=>true
				],
			])->fetchAll();
		return $this;
		}

	private function addDispatcher(){
		$res=TestDispatcher\TestdispatcherTable::add([
			'UF_ACTIVE'=>true,
			'UF_USER'=>2,
			'UF_COMMENT'=>'Dispatcher comment',
			'UF_RIGHTS'=>12
		]);
		if(!$res->isSuccess()){
			//echo"<pre>".print_r($res->getErrorMessages(),true)."</pre>";
		}
	}

	private function addObject(){
		$res=TestDispatcher\TestobjectTable::add([
			'UF_NAME'=>'New object',
			'UF_ADDRESS'=>'New object address',
			'UF_COMMENT'=>'Object comment'
		]);
		if(!$res->isSuccess()){
			//echo"<pre>".print_r($res->getErrorMessages(),true)."</pre>";
		}
	}

	private function addLink(){
		$res=TestDispatcher\TestdisobTable::add([
			'UF_USER'=>2,
			'UF_OBJECT'=>1,
		]);
		if(!$res->isSuccess()){
			//echo"<pre>".print_r($res->getErrorMessages(),true)."</pre>";
		}
	}

	public function executeComponent(){
		/* тестовая информация */
		$this->addDispatcher();
		$this->addObject();
		$this->addLink();
		/* /тестовая информация  */
		$this->error=new ErrorCollection();
		$this->arResult['ERRORS']=$this->getErrors();
		if($this->startResultCache($this->arParams['CACHE_TIME'])){
			$this->getList();
			$this->includeComponentTemplate();
			}
		}
	}