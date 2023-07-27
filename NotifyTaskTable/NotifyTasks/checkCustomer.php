<?php
namespace Chelnynet\Warranty\NotifyTasks;

use Bitrix\Main\Type\DateTime;

/**
 * Class refundByContractor
 * @package Chelnynet\Warranty\NotifyTasks
  */

class checkCustomer extends NotifyTasks{

	public function setVariables($attr=null): self
	{
		$time=$this->nineFourteen('+ 10 days');
		$this->start=new DateTime($time);
		$this->nextExec=new DateTime($time);
		$this->users=$this->managersList();
		$this->users[]=$this->authorID();
		return $this;
	}

	public function removeTaskFilter($attr=null): self
	{
		$this->filter=[
			'=UF_RECLAMATION'=>$this->reclamation,
			'=UF_TYPE'=>get_called_class()
			];
		return $this;
	}

	public function getTemplate(): self
	{
		$this->messageTemplate='NOTIFY_TASK_ENTITY_SHIPPING_CHECK_MESSAGE';
		return $this;
	}

	public function getNextExec(array $item): self
	{
		$this->nextExec=new DateTime($this->nineFourteen());
		return $this;
	}
}