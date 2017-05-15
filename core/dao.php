<?php
defined("IN_XIAOSHU") or exit("Access Invalid!");
abstract class Dao {

	private $db;
	public function __construct() {
		$this -> db = \Core::db();
	}

	/**
	 * 设置Dao中使用的数据库操作对象
	 * @param Database_ActiveRecord $db
	 * @return \Dao
	 */
	public function setDb(DataBase_ActiveRecord $db) {
		$this -> db = $db;
		return $this;
	}

	/**
	 * 获取Dao中使用的数据库操作对象
	 * @return Database_ActiveRecord
	 */
	public function & getDb() {
		return $this -> db;
	}

	public abstract function getTable();
	public abstract function getPrimaryKey();
	public abstract function getColumns();
	/**
	 * 添加数据
	 * @param array $data  需要添加的数据
	 * @param boolean $returnLastId 是否返回lastid;
	 * @return int 最后插入的id，失败为0
	 */
	public function insert($data,$returnLastId=true) {
		$num = $this -> getDb() -> insert($this -> getTable(), $data) -> execute();
		if($returnLastId){
			return $num ? $this -> getDb() -> lastId() : 0;
		}
		return $num;
	}

	/**
	 * 批量添加数据
	 * @param array $rows  需要添加的数据
	 * @return int 插入的数据中第一条的id，失败为0
	 */
	public function insertBatch($rows) {
		$num = $this -> getDb() -> insertBatch($this -> getTable(), $rows) -> execute();
		return $num ? $this -> getDb() -> lastId() : 0;
	}

	/**
	 * 更新数据
	 * @param type $data  需要更新的数据
	 * @param type $where     可以是where条件关联数组，还可以是主键值。
	 * @return boolean
	 */
	public function update($data, $where) {
		$where = is_array($where) ? $where : array($this -> getPrimaryKey() => $where);
		return $this -> getDb() -> where($where) -> update($this -> getTable(), $data) -> execute();
	}

	/**
	 * 更新数据
	 * @param type $data  需要批量更新的数据
	 * @param type $index  需要批量更新的数据中的主键名称
	 * @return boolean
	 */
	public function updateBatch($data, $index) {
		return $this -> getDb() -> updateBatch($this -> getTable(), $data, $index) -> execute();
	}

	/**
	 * 获取一条或者多条数据
	 * @param type $values      可以是一个主键的值或者主键的值数组，还可以是where条件
	 * @param boolean $isRows  返回多行记录还是单行记录，true：多行，false：单行
	 * @param type $orderBy    当返回多行记录时，可以指定排序，
	 * 			     比如：array('time'=>'desc')或者array('time'=>'desc','id'=>'asc')
	 * @return int
	 */
	public function find($values, $isRows = false, Array $orderBy = array()) {
		if (empty($values)) {
			return 0;
		}
		if (is_array($values)) {
			$is_asso = array_diff_assoc(array_keys($values), range(0, sizeof($values))) ? TRUE : FALSE;
			if ($is_asso) {
				$this -> getDb() -> where($values);
			} else {
				$this -> getDb() -> where(array($this -> getPrimaryKey() => array_values($values)));
			}
		} else {
			$this -> getDb() -> where(array($this -> getPrimaryKey() => $values));
		}
		foreach ($orderBy as $k => $v) {
			$this -> getDb() -> orderBy($k, $v);
		}
		if (!$isRows) {
			$this -> getDb() -> limit(0, 1);
		}
		$rs = $this -> getDb() -> from($this -> getTable()) -> execute();
		if ($isRows) {
			return $rs -> rows();
		} else {
			return $rs -> row();
		}
	}

	/**
	 * 获取所有数据
	 * @param type $where   where条件数组
	 * @param type $orderBy 排序，比如：array('time'=>'desc')或者array('time'=>'desc','id'=>'asc')
	 * @param type $limit   limit数量，比如：10
	 * @param type $fields  要搜索的字段，比如：id,name。留空默认*
	 * @return type
	 */
	public function findAll($where = null, Array $orderBy = array(), $limit = null, $fields = null) {
		if (!is_null($fields)) {
			$this -> getDb() -> select($fields);
		}
		if (!is_null($where)) {
			$this -> getDb() -> where($where);
		}
		foreach ($orderBy as $k => $v) {
			$this -> getDb() -> orderBy($k, $v);
		}
		if (!is_null($limit)) {
			$this -> getDb() -> limit(0, $limit);
		}
		return $this -> getDb() -> from($this -> getTable()) -> execute() -> rows();
	}

	/**
	 * 根据条件获取一个字段的值或者数组
	 * @param type $col         字段名称
	 * @param type $where       可以是一个主键的值或者主键的值数组，还可以是where条件
	 * @param boolean $isRows  返回多行记录还是单行记录，true：多行，false：单行
	 * @param type $orderBy    当返回多行记录时，可以指定排序，比如：array('time'=>'desc')或者array('time'=>'desc','id'=>'asc')
	 * @return type
	 */
	public function findCol($col, $where, $isRows = false, Array $orderBy = array()) {
		$row = $this -> find($where, $isRows, $orderBy);
		if (!$isRows) {
			return isset($row[$col]) ? $row[$col] : null;
		} else {
			$vals = array();
			foreach ($row as $v) {
				$vals[] = $v[$col];
			}
			return $vals;
		}
	}

	/**
	 *
	 * 根据条件删除记录
	 * @param type $values 可以是一个主键的值或者主键主键的值数组
	 * @param type $cond   附加的where条件，关联数组
	 * 成功则返回影响的行数，失败返回false
	 */
	public function delete($values, $cond = NULL) {
		if (!empty($values)) {
			$this -> getDb() -> where(array($this -> getPrimaryKey() => is_array($values) ? array_values($values) : $values));
		}
		if (!empty($cond)) {
			$this -> getDb() -> where($cond);
		}
		return $this -> getDb() -> delete($this -> getTable()) -> execute();
	}

	/**
	 * 分页方法
	 * @param int $page       第几页
	 * @param int $pagesize   每页多少条
	 * @param string $url     基础url，里面的{page}会被替换为实际的页码
	 * @param string $fields  select的字段，全部用*，多个字段用逗号分隔
	 * @param array  $where    where条件，关联数组
	 * @param string $orderBy 排序字段，比如：array('time'=>'desc')或者array('time'=>'desc','id'=>'asc')
	 * @param array $pageBarOrder   分页条组成，可以参考手册分页条部分
	 * @param int   $pageBarACount 分页条a的数量，可以参考手册分页条部分
	 * @return type
	 */
	public function getPage($page, $pagesize, $url, $fields = '*', Array $where = null, Array $orderBy = array(), $pageBarOrder = array(1, 2, 3, 4, 5, 6), $pageBarACount = 10) {
		$data = array();
		if (is_array($where)) {
			$this -> getDb() -> where($where);
		}
		$total = $this -> getDb() -> select('count(*) as total') -> from($this -> getTable()) -> execute() -> value('total');
		//这里必须重新附加条件，上面的count会重置条件
		if (is_array($where)) {
			$this -> getDb() -> where($where);
		}
		foreach ($orderBy as $k => $v) {
			$this -> getDb() -> orderBy($k, $v);
		}
		$data['items'] = $this -> getDb() -> select($fields) -> limit(($page - 1) * $pagesize, $pagesize) -> from($this -> getTable()) -> execute() -> rows();
		$data['page'] = \Core::page($total, $page, $pagesize, $url, $pageBarOrder, $pageBarACount);
		return $data;
	}
	
	/*
	 * 获取flexigrid分页方法
	 */ 
	public function getFlexPage($page,$pagesize,$fields = '*', Array $where = null, Array $orderBy = array()){
		$data = array();
		if (is_array($where)) {
			$this -> getDb() -> where($where);
		}
		$total = $this -> getDb() -> select('count(*) as total') -> from($this -> getTable()) -> execute() -> value('total');
		//这里必须重新附加条件，上面的count会重置条件
		if (is_array($where)) {
			$this -> getDb() -> where($where);
		}
		foreach ($orderBy as $k => $v) {
			$this -> getDb() -> orderBy($k, $v);
		}
		$data['total']=$total;
		$data['rows'] = $this -> getDb() -> select($fields) -> limit(($page - 1) * $pagesize, $pagesize) -> from($this -> getTable()) -> execute() ->rows();

		return $data;
	}

	/**
	 * SQL搜索
	 * @param type $page      第几页
	 * @param type $pagesize  每页多少条
	 * @param type $url       基础url，里面的{page}会被替换为实际的页码
	 * @param type $fields    select的字段，全部用*，多个字段用逗号分隔
	 * @param type $cond      是条件字符串，SQL语句where后面的部分，不要带limit
	 * @param type $values    $cond中的问号的值数组，$cond中使用?可以防止sql注入
	 * @param array $pageBarOrder   分页条组成，可以参考手册分页条部分
	 * @param int   $pageBarACount 分页条a的数量，可以参考手册分页条部分
	 * @return type
	 */
	public function search($page, $pagesize, $url, $fields, $cond, Array $values = array(), $pageBarOrder = array(1, 2, 3, 4, 5, 6), $pageBarACount = 10) {
		$data = array();
		$table = $this -> getDb() -> getTablePrefix() . $this -> getTable();
		$rs = $this -> getDb() -> execute('select count(*) as total from ' . $table . (strpos(trim($cond), 'order') === 0 ? ' ' : ' where ') . $cond, $values);
		//如果 $cond 包含 group by，结果条数是$rs->total()
		$total = $rs -> total() > 1 ? $rs -> total() : $rs -> value('total');
		$data['items'] = $this -> getDb() -> execute('select ' . $fields . ' from ' . $table . (strpos(trim($cond), 'order') === 0 ? ' ' : ' where ') . $cond . ' limit ' . (($page - 1) * $pagesize) . ',' . $pagesize, $values) -> rows();
		$data['page'] = \Core::page($total, $page, $pagesize, $url, $pageBarOrder, $pageBarACount);
		return $data;
	}

}
?>