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
		$amqp=\Core::library('XSQueue/QueueManager');
		$amqpConn=$amqp->queue();
		$amqpContext=$amqpConn->createContext();
		$workTopic = $amqpContext->createTopic('test.amqp.work');
		$workTopic->addFlag(AMQP_DURABLE);//持久化
		$workTopic->setType(AMQP_EX_TYPE_DIRECT);//直连
		//$amqpContext->deleteTopic($workTopic);
		$amqpContext->declareTopic($workTopic);
		$workQueue = $amqpContext->createQueue('test.work');
		$workQueue->addFlag(AMQP_DURABLE);
		//$amqpContext->deleteQueue($workQueue);
		$amqpContext->declareQueue($workQueue);
		$amqpContext->bind($workTopic, $workQueue);
		//声明工作队列
		$producer=$amqpContext->createProducer();
		//发送10条信息
		$i=0;
		for($i=0;$i<10;$i++){
			$message = $amqpContext->createMessage("work{$i}");
			$producer->send($workQueue, $message);
			echo "send work{$i}<br>";
		}
		$amqpContext->close();
	}
	
	//工作队列接收A
	public function do_queuetest_task_A(){
		$amqp=\Core::library('XSQueue/QueueManager');
		$amqpConn=$amqp->queue();
		$amqpContext=$amqpConn->createContext();
		$queue = $amqpContext->createQueue('test.work');
		$consumer = $amqpContext->createConsumer($queue);
		$m = $consumer->receive(1);
		if($m){
			$consumer->acknowledge($m);
			echo "receive ".$m->getBody();
		}else{
			echo "no data yet";
		}
		$amqpContext->close();
	}
	
	//工作队列接收B
	public function do_queuetest_task_B(){
		$amqp=\Core::library('XSQueue/QueueManager');
		$amqpConn=$amqp->queue();
		$amqpContext=$amqpConn->createContext();
		$queue = $amqpContext->createQueue('test.work');
		$consumer = $amqpContext->createConsumer($queue);
		$m = $consumer->receive(1);
		if($m){
			$consumer->acknowledge($m);
			echo "receive ".$m->getBody();
		}else{
			echo "no data yet";
		}
		$amqpContext->close();
	}
	
	//消息队列
	public function do_queuetest_direct(){
		$amqp=\Core::library('XSQueue/QueueManager');
		$amqpConn=$amqp->queue();
		$amqpContext=$amqpConn->createContext();
		$workTopic = $amqpContext->createTopic('test.amqp.work');
		$workTopic->addFlag(AMQP_DURABLE);//持久化
		$workTopic->setType(AMQP_EX_TYPE_DIRECT);//直连
		$amqpContext->declareTopic($workTopic);
		$workQueueA = $amqpContext->createQueue('test.work.A');
		$workQueueA->addFlag(AMQP_DURABLE);
		$amqpContext->declareQueue($workQueueA);
		$amqpContext->bind($workTopic, $workQueueA);
		$workQueueB = $amqpContext->createQueue('test.work.B');
		$workQueueB->addFlag(AMQP_DURABLE);
		$amqpContext->declareQueue($workQueueB);
		$amqpContext->bind($workTopic, $workQueueB);
		//声明工作队列
		$producer=$amqpContext->createProducer();
		//发送10条信息
		$i=0;
		for($i=0;$i<10;$i++){
			$messageA = $amqpContext->createMessage("directA{$i}");
			$messageB = $amqpContext->createMessage("directB{$i}");
			$producer->send($workQueueA, $messageA);
			$producer->send($workQueueB, $messageB);
			echo "send directA{$i}<br>";
			echo "send directB{$i}<br>";
		}
		$amqpContext->close();
	}

	public function do_queuetest_direct_A(){
		$amqp=\Core::library('XSQueue/QueueManager');
		$amqpConn=$amqp->queue();
		$amqpContext=$amqpConn->createContext();
		$queue = $amqpContext->createQueue('test.work.A');
		$consumer = $amqpContext->createConsumer($queue);
		$m = $consumer->receive(1);
		if($m){
			$consumer->acknowledge($m);
			echo "receive ".$m->getBody();
		}else{
			echo "no data yet";
		}
		$amqpContext->close();
	}
	
	public function do_queuetest_direct_B(){
		$amqp=\Core::library('XSQueue/QueueManager');
		$amqpConn=$amqp->queue();
		$amqpContext=$amqpConn->createContext();
		$queue = $amqpContext->createQueue('test.work.B');
		$consumer = $amqpContext->createConsumer($queue);
		$m = $consumer->receive(1);
		if($m){
			$consumer->acknowledge($m);
			echo "receive ".$m->getBody();
		}else{
			echo "no data yet";
		}
		$amqpContext->close();
	}
	
	//广播
	public function do_queuetest_boardcast(){
		$amqp=\Core::library('XSQueue/QueueManager');
		$amqpConn=$amqp->queue();
		$amqpContext=$amqpConn->createContext();
		$workTopic = $amqpContext->createTopic('test.boardcast');
		$workTopic->addFlag(AMQP_DURABLE);//持久化
		$workTopic->setType(AMQP_EX_TYPE_FANOUT);//广播
		$amqpContext->declareTopic($workTopic);
		//声明2个空队列
		$workQueueA = $amqpContext->createQueue('test.boardcast.A');
		$workQueueA->addFlag(AMQP_DURABLE);
		$amqpContext->declareQueue($workQueueA);
		$amqpContext->bind($workTopic, $workQueueA);
		$workQueueB = $amqpContext->createQueue('test.boardcast.B');
		$workQueueB->addFlag(AMQP_DURABLE);
		$amqpContext->declareQueue($workQueueB);
		$amqpContext->bind($workTopic, $workQueueB);
		
		
		//声明队列
		$producer=$amqpContext->createProducer();
		//发送10条广播信息
		$i=0;
		for($i=0;$i<10;$i++){
			$message = $amqpContext->createMessage("boardcast{$i}");
			$producer->send($workTopic, $message);
			echo "send boardcast{$i}<br>";
		}
		$amqpContext->close();
	}
	
	public function do_queuetest_boardcast_A(){
		$amqp=\Core::library('XSQueue/QueueManager');
		$amqpConn=$amqp->queue();
		$amqpContext=$amqpConn->createContext();
		$queue = $amqpContext->createQueue('test.boardcast.A');
		$consumer = $amqpContext->createConsumer($queue);
		$m = $consumer->receive(1);
		if($m){
			$consumer->acknowledge($m);
			echo "receive ".$m->getBody();
		}else{
			echo "no data yet";
		}
		$amqpContext->close();
	}
	
	public function do_queuetest_boardcast_B(){
		$amqp=\Core::library('XSQueue/QueueManager');
		$amqpConn=$amqp->queue();
		$amqpContext=$amqpConn->createContext();
		$queue = $amqpContext->createQueue('test.boardcast.B');
		$consumer = $amqpContext->createConsumer($queue);
		$m = $consumer->receive(1);
		if($m){
			$consumer->acknowledge($m);
			echo "receive ".$m->getBody();
		}else{
			echo "no data yet";
		}
		$amqpContext->close();
	}
	
	
	
}
?>