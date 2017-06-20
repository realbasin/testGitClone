<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');
class  controller_dashboard extends controller_sysBase {
	public function do_index() {

	}
	
	
	///////////////-------------以下为测试代码--------------------------------------------
	
	public function do_locktest(){
		\Core::view()->load('test_locktest');
	}
	
	public function do_lockA(){
		//初始化换一个锁管理对象
		$lockManage=\Core::library("Lock_LockManager");
		//实例化一个具体的锁类型,不填写锁类型，则使用默认配置
		//$lock=$lockManage->lockStore();
		//$lock=$lockManage->lockStore('memcached');
		$lock=$lockManage->lockStore();
		//$lock=$lockManage->lockStore('redis_cluster');
//		$lock=$lockManage->lockStore('file');//本地测试用file
		//锁采用闭包封装，注意传参 function()传参使用 use()
		$lock->granule('filekey',function(){
			sleep(8);
			echo('上锁完成，输出A');
		});
	}
	
	public function do_lockB(){
		//初始化换一个锁管理对象
		$lockManage=\Core::library("Lock_LockManager");
		//实例化一个具体的锁类型,不填写锁类型，则使用默认配置
		//$lock=$lockManage->lockStore();
		//$lock=$lockManage->lockStore('memcached');
		$lock=$lockManage->lockStore();
		//$lock=$lockManage->lockStore('redis_cluster');
//		$lock=$lockManage->lockStore('file');//本地测试用file
		//锁采用闭包封装，注意传参 function()传参使用 use()
		$lock->granule('filekey',function(){
			sleep(1);
			echo('上锁完成，输出B');
		});
	}
	
	//队列测试
	public function do_queuetest(){
		\Core::view()->load('test_queuetest');
	}
	
	//工作队列，就是1个队列，多个消费者
	public function do_queuetest_task(){
		$amqp=\Core::library('AmqpLib/AmqpManager');
		//注意，如果已经声明了队列，下面这一句是非必须的
		$amqp->bindQueueToExchangeDirect('test.work','test.amqp.work');
		for($i=0;$i<10;$i++){
			$amqp->publishDirect('test.work', "work{$i}");
			echo "send work{$i}<br>";
		}
		$amqp->close();
	}
	
	//工作队列接收A
	public function do_queuetest_task_A(){
		$amqp=\Core::library('AmqpLib/AmqpManager');
		
		$m = $amqp->getOne('test.amqp.work',true);//这里做自动应答
		if($m){
			echo "receive ".$m->body;
		}else{
			echo "no data";
		}
		$amqp->close();
	}
	
	//工作队列接收B
	public function do_queuetest_task_B(){
		$amqp=\Core::library('AmqpLib/AmqpManager');
		
		$m = $amqp->getOne('test.amqp.work',true);
		if($m){
			echo "receive ".$m->body;
		}else{
			echo "no data";
		}
		$amqp->close();
	}
	
	//direct队列
	public function do_queuetest_direct_sendA(){
		$amqp=\Core::library('AmqpLib/AmqpManager');
		//注意，如果已经声明了队列，下面这一句是非必须的
		$amqp->bindQueueToExchangeDirect('test.direct','test.amqp.fruit','apple');
		$amqp->bindQueueToExchangeDirect('test.direct','test.amqp.fruit','banana');
		
		$amqp->bindQueueToExchangeDirect('test.direct','test.amqp.banana','banana');
		
		$amqp->publishDirect('test.direct', "i love apple","apple");
		echo "send i love apple<br>";
		
		$amqp->close();
	}
	
	public function do_queuetest_direct_sendB(){
		$amqp=\Core::library('AmqpLib/AmqpManager');
		//注意，如果已经声明了队列，下面这一句是非必须的
		$amqp->bindQueueToExchangeDirect('test.direct','test.amqp.fruit','apple');
		$amqp->bindQueueToExchangeDirect('test.direct','test.amqp.fruit','banana');
		
		$amqp->bindQueueToExchangeDirect('test.direct','test.amqp.banana','banana');
		
		$amqp->publishDirect('test.direct', "i love banana","banana");
		echo "send i love banana<br>";
		
		$amqp->close();
	}

	public function do_queuetest_direct_A(){
		$amqp=\Core::library('AmqpLib/AmqpManager');
		
		$m = $amqp->getOne('test.amqp.fruit',true);//这里做自动应答
		if($m){
			echo "receive ".$m->body;
		}else{
			echo "no data";
		}
		$amqp->close();
	}
	
	public function do_queuetest_direct_B(){
		$amqp=\Core::library('AmqpLib/AmqpManager');
		
		$m = $amqp->getOne('test.amqp.banana',true);//这里做自动应答
		if($m){
			echo "receive ".$m->body;
		}else{
			echo "no data";
		}
		$amqp->close();
	}
	
	//广播
	public function do_queuetest_boardcast(){
		$amqp=\Core::library('AmqpLib/AmqpManager');
		//注意，如果已经声明了队列，下面这一句是非必须的
		$amqp->bindQueueToExchangeFanout('test.fanout','test.amqp.fanoutA');
		$amqp->bindQueueToExchangeFanout('test.fanout','test.amqp.fanoutB');
		
		for($i=0;$i<10;$i++){
			$amqp->publishFanout('test.fanout', "fanout{$i}");
			echo "send fanout{$i}<br>";
		}
		
		$amqp->close();
	}
	
	public function do_queuetest_boardcast_A(){
		$amqp=\Core::library('AmqpLib/AmqpManager');
		
		$m = $amqp->getOne('test.amqp.fanoutA',true);//这里做自动应答
		if($m){
			echo "receive ".$m->body;
		}else{
			echo "no data";
		}
		$amqp->close();
	}
	
	public function do_queuetest_boardcast_B(){
		$amqp=\Core::library('AmqpLib/AmqpManager');
		
		$m = $amqp->getOne('test.amqp.fanoutB',true);//这里做自动应答
		if($m){
			echo "receive ".$m->body;
		}else{
			echo "no data";
		}
		$amqp->close();
	}
	
	public function do_queuetest_topic_sendA(){
		$amqp=\Core::library('AmqpLib/AmqpManager');
		//注意，如果已经声明了队列，下面这一句是非必须的
		$amqp->bindQueueToExchangeTopic('test.topic','test.amqp.topicA','#.log');
		$amqp->bindQueueToExchangeTopic('test.topic','test.amqp.topicB','mail.#');
		
		$amqp->publishTopic('test.topic', "this is a mail log","mail.log");
		echo "this is a mail log<br>";
		
		$amqp->close();
	}
	
	public function do_queuetest_topic_sendB(){
		$amqp=\Core::library('AmqpLib/AmqpManager');
		//注意，如果已经声明了队列，下面这一句是非必须的
		$amqp->bindQueueToExchangeTopic('test.topic','test.amqp.topicA','#.log');
		$amqp->bindQueueToExchangeTopic('test.topic','test.amqp.topicB','mail.#');
		
		$amqp->publishTopic('test.topic', "this is a mobile log","mobile.log");
		echo "this is a mobile log<br>";
		
		$amqp->close();
	}
	
	public function do_queuetest_topic_sendC(){
		$amqp=\Core::library('AmqpLib/AmqpManager');
		//注意，如果已经声明了队列，下面这一句是非必须的
		$amqp->bindQueueToExchangeTopic('test.topic','test.amqp.topicA','#.log');
		$amqp->bindQueueToExchangeTopic('test.topic','test.amqp.topicB','mail.#');
		
		$amqp->publishTopic('test.topic', "this is a mail send","mail.send");
		echo "this is a mail send<br>";
		
		$amqp->close();
	}
	
	public function do_queuetest_topic_A(){
		$amqp=\Core::library('AmqpLib/AmqpManager');
		
		$m = $amqp->getOne('test.amqp.topicA',true);//这里做自动应答
		if($m){
			echo "receive ".$m->body;
		}else{
			echo "no data";
		}
		$amqp->close();
	}
	
	public function do_queuetest_topic_B(){
		$amqp=\Core::library('AmqpLib/AmqpManager');
		
		$m = $amqp->getOne('test.amqp.topicB',true);//这里做自动应答
		if($m){
			echo "receive ".$m->body;
		}else{
			echo "no data";
		}
		$amqp->close();
	}
	
	public function do_queuetest_delay(){
		$amqp=\Core::library('AmqpLib/AmqpManager');
		//注意，如果已经声明了队列，下面这一句是非必须的
		$amqp->bindQueueToExchangeDelay('test.delay','test.amqp.delay');
		$amqp->publishDelay('test.delay', "this message delay 10s",'',0,0,10);
		echo "send delay message<br>";
		$amqp->close();
	}
	
	public function do_queuetest_delay_A(){
		$amqp=\Core::library('AmqpLib/AmqpManager');
		
		$amqp->consume('test.amqp.delay',function($message){
			echo "receive ".$message->body;
		},true);//这里做自动应答
		
		$amqp->close();
	}
	
	public function do_queuetest_delay_B(){
		$amqp=\Core::library('AmqpLib/AmqpManager');
		
		$m = $amqp->getOne('test.amqp.delay',true);//这里做自动应答
		if($m){
			echo "receive ".$m->body;
		}else{
			echo "no data";
		}
		$amqp->close();
	}

	public function do_queuetest_pr_sendA(){
		$amqp=\Core::library('AmqpLib/AmqpManager');
		//注意，如果已经声明了队列，下面这一句是非必须的
		$amqp->bindQueueToExchangeDirect('test.pr','test.amqp.pr');
		$amqp->publishDirect('test.pr', "this message is common");
		echo "this message is common<br>";
		$amqp->close();
	}
	
	public function do_queuetest_pr_sendB(){
		$amqp=\Core::library('AmqpLib/AmqpManager');
		//注意，如果已经声明了队列，下面这一句是非必须的
		$amqp->bindQueueToExchangeDirect('test.pr','test.amqp.pr');
		$amqp->publishDirect('test.pr', "this message is priority",'',5);
		echo "this message is priority<br>";
		$amqp->close();
	}

	public function do_queuetest_pr_A(){
		$amqp=\Core::library('AmqpLib/AmqpManager');
		
		$m = $amqp->getOne('test.amqp.pr',true);//这里做自动应答
		if($m){
			echo "receive ".$m->body;
		}else{
			echo "no data";
		}
		$amqp->close();
	}
	
	
	
}
?>