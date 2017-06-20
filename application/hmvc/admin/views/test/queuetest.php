<h1>全部接收端打开后刷新可以看到下一条信息</h1>
<br><br>
工作队列（一个队列多个消费者）
使用方法：1.每次点发送消息会发送10条消息入队
<br>2.分别点击A，B接收端后进行不断刷新，会看到A,B接收端会接收到work0-9不同的内容
<br><br>
<a target="_blank" href="<?php echo adminUrl('dashboard','queuetest_task');?>">发送消息</a>
<br><br>
<a target="_blank" href="<?php echo adminUrl('dashboard','queuetest_task_A');?>">A接收端</a>
<a target="_blank" href="<?php echo adminUrl('dashboard','queuetest_task_B');?>">B接收端</a>
<br><br>
Direct队列（发送消息路由个指定routing_key的对列，一个队列可以绑定多个routing_key）
使用方法：1.点击发送不同的消息到指定的routing_key
<br>2.分别点击接收端进行接收，打开接收端后，刷新
<br><br>
<a target="_blank" href="<?php echo adminUrl('dashboard','queuetest_direct_sendA');?>">发送消息给routing_key="apple"</a>
<a target="_blank" href="<?php echo adminUrl('dashboard','queuetest_direct_sendB');?>">发送消息给routing_key="banana"</a>
<br><br>
<a target="_blank" href="<?php echo adminUrl('dashboard','queuetest_direct_A');?>">接收端(能收到apple,banana)</a>
<a target="_blank" href="<?php echo adminUrl('dashboard','queuetest_direct_B');?>">接收端(只能收到banana)</a>
<br><br>
订阅/广播（生产者会发送消息到所有绑定的队列，所有消费者都能接收到同样的消息）
使用方法：1.每次点发送消息会广播10条消息到A,B队列
<br>2.分别点击A，B队列接收端后会看到，A,B队列接收端能接收到一样的消息
<br>3.A,B分别接收不影响其它队列的接收内容
<br><br>
<a target="_blank" href="<?php echo adminUrl('dashboard','queuetest_boardcast');?>">发送广播</a>
<br><br>
<a target="_blank" href="<?php echo adminUrl('dashboard','queuetest_boardcast_A');?>">A队列接收端</a>
<a target="_blank" href="<?php echo adminUrl('dashboard','queuetest_boardcast_B');?>">B队列接收端</a>
<br><br>
TOPIC路由（发送消息到指定的routing_key，队列可以根据通配符来获得消息）<br>
.符号为*通配符的分隔符,*通配符表示一个单词，#通配符表示任意长度字符
如，队列的绑定的routing_key是#.log，则该队列可以接收routing_key=mail.log的消息，也可以接收xssd.mail.log的消息
如，队列的绑定的routing_key是*.log，则该队列可以接收routing_key=mail.log的消息，可以接收mobile.log的消息，但不可以接收xssd.mail.log的消息
<br><br>
<a target="_blank" href="<?php echo adminUrl('dashboard','queuetest_topic_sendA');?>">发送消息给(mail.log)</a> 
<a target="_blank" href="<?php echo adminUrl('dashboard','queuetest_topic_sendB');?>">发送消息给(mobile.log)</a>
<a target="_blank" href="<?php echo adminUrl('dashboard','queuetest_topic_sendC');?>">发送消息给(mail.send)</a>
<br><br>
<a target="_blank" href="<?php echo adminUrl('dashboard','queuetest_topic_A');?>">接收端(#.log)</a>
<a target="_blank" href="<?php echo adminUrl('dashboard','queuetest_topic_B');?>">接收端(mail.#)</a>
<br><br>
延时发送<br>
使用方法：1.点击发送消息，消息会延时10秒发出
<br>2.点击接收端，接收端使用阻塞式接收，到预定时间则收到消息
<br><br>
<a target="_blank" href="<?php echo adminUrl('dashboard','queuetest_delay');?>">发送消息</a>
<br><br>
<a target="_blank" href="<?php echo adminUrl('dashboard','queuetest_delay_A');?>">阻塞式接收端（页面一直等待直到消息过来，请先发送消息）</a>
<a target="_blank" href="<?php echo adminUrl('dashboard','queuetest_delay_B');?>">非阻塞式接收端（有消息则显示，无消息显示no data）</a>
<br><br>
优先级
使用方法：1.点击发送消息，发送一个普通消息<br>
1.点击发送优先级消息，发送一个插队消息
<br>2.点击接收端，可以看到，会先接收到优先级的消息
<br><br>
<a target="_blank" href="<?php echo adminUrl('dashboard','queuetest_pr_sendA');?>">发送普通消息</a>
<a target="_blank" href="<?php echo adminUrl('dashboard','queuetest_pr_sendB');?>">发送优先级消息</a>
<br><br>
<a target="_blank" href="<?php echo adminUrl('dashboard','queuetest_pr_A');?>">接收端</a>
<br><br>
手动应答（上面的例子都是使用自动应答）<br>
不应答的消息不会删除，一直存在队列之中<br>
正式代码基本上都使用手动应答
<br><br>
<a target="_blank" href="<?php echo adminUrl('dashboard','queuetest_ack');?>">发送5条消息</a>
<br><br>
<a target="_blank" href="<?php echo adminUrl('dashboard','queuetest_ack_A');?>">接收端（不应答，消息还在）</a>
<a target="_blank" href="<?php echo adminUrl('dashboard','queuetest_ack_A');?>">接收端（应答，删除消息）</a>
<br><br>
消息撤回队列<br>
实际应用中，可能因为网络波动或某个数据行错误，导致无法操作。这个时候将消息撤回到队列尾部，稍后再进行操作
<br><br>
<a target="_blank" href="<?php echo adminUrl('dashboard','queuetest_cancel');?>">发送5消息</a>
<br><br>
<a target="_blank" href="<?php echo adminUrl('dashboard','queuetest_cancel_A');?>">接收端（撤回消息，不断刷新看到循环消息）</a>
<br><br>