<?php
defined("IN_XIAOSHU") or exit("Access Invalid!");
/*
 * PDO扩展类
 * */
class Xs_PDO extends PDO {
	protected $transactionCounter = 0;
	private $isLast;
	public function isInTransaction() {
		return !$this -> isLast;
	}

	public function beginTransaction() {
		if (!$this -> transactionCounter++) {
			return parent::beginTransaction();
		}
		$this -> exec('SAVEPOINT trans' . $this -> transactionCounter);
		return $this -> transactionCounter >= 0;
	}

	public function commit() {
		if (!--$this -> transactionCounter) {
			$this -> isLast = true;
			return parent::commit();
		}
		$this -> isLast = false;
		return $this -> transactionCounter >= 0;
	}

	public function rollback() {
		if (--$this -> transactionCounter) {
			$this -> exec('ROLLBACK TO trans' . $this -> transactionCounter + 1);
			return true;
		}
		return parent::rollback();
	}

}
?>