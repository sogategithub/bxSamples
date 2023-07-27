<?php
namespace Chelnynet\Warranty\NotifyTasks;

use Bitrix\Hlbd\WarrantyproductsTable;
use Bitrix\Main\Type\DateTime;

/**
 * Class checkBuyerInfo
 * @package Chelnynet\Warranty\NotifyTasks
 */

class checkBuyerInfo extends NotifyTasks{

	protected function hasProducts($filter=[]):bool
	{
		$filter=\array_merge($filter,['=UF_RECLAMATION'=>$this->reclamation]);
		$result=WarrantyproductsTable::getCount($filter);
		if($result>0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public function setVariables($attr=null):self
	{
		$hasProducts=$this->hasProducts(['=UF_TROUBLE_FIND'=>[38,39]]);
		if(!$hasProducts)
		{
			$this->resume=false;
			return $this;
		}

		$time=$this->nineFourteen();
		$this->start=new DateTime($time);
		$this->nextExec=new DateTime($time);

		$this->users=$this->managersList();
		return $this;
	}

	public function removeTaskFilter($attr=null):self
	{
		$this->filter=[
			'=UF_RECLAMATION'=>$this->reclamation,
			'=UF_TYPE'=>get_called_class()
		];
		return $this;
	}

	public function getTemplate(): self
	{
		$this->messageTemplate='NOTIFY_TASK_ENTITY_CHECK_BUYER_INFO_MESSAGE';
		return $this;
	}

	public function getNextExec(array $item): self
	{
		$this->nextExec=new DateTime($this->nineFourteen());
		return $this;
	}
}