<h1>全部接收端打开后刷新可以看到下一条信息</h1>
<br><br>
工作队列（一个队列多个消费者）
使用方法：1.每次点发送消息会清空队列后发送10条消息入队
<br>2.分别点击A，B接收端后进行不断刷新，会看到A,B接收端会接收到work0-9不同的内容
<br><br>
<a target="_blank" href="<?php echo adminUrl('dashboard','queuetest_task');?>">发送消息</a>
<br><br>
<a target="_blank" href="<?php echo adminUrl('dashboard','queuetest_task_A');?>">A接收端</a>
<a target="_blank" href="<?php echo adminUrl('dashboard','queuetest_task_B');?>">B接收端</a>
<br><br>
订阅/广播（生产者会发送消息到所有已知的队列，所有消费者都能接收到同样的消息）
使用方法：1.每次点发送消息会广播10条消息到A,B队列
<br>2.分别点击A，B队列接收端后会看到，A,B队列接收端能接收到一样的消息
<br>3.A,B分别接收不影响其它队列的接收内容
<br><br>
<a target="_blank" href="<?php echo adminUrl('dashboard','queuetest_boardcast');?>">发送广播</a>
<br><br>
<a target="_blank" href="<?php echo adminUrl('dashboard','queuetest_boardcast_A');?>">A队列接收端</a>
<a target="_blank" href="<?php echo adminUrl('dashboard','queuetest_boardcast_B');?>">B队列接收端</a>
<br><br>
消息路由（生产者发送消息，根据消息的不同路由到不同的队列）
使用方法：1.每次点发送消息会分别给A,B发送不同的信息
<br>2.分别点击A，B队列接收端后会看到，A,B队列接收端能接收到不同的消息
<br>3.A,B分别接收不影响其它队列的接收内容
<br><br>
<a target="_blank" href="<?php echo adminUrl('dashboard','queuetest_direct');?>">发送消息</a>
<br><br>
<a target="_blank" href="<?php echo adminUrl('dashboard','queuetest_direct_A');?>">A接收端</a>
<a target="_blank" href="<?php echo adminUrl('dashboard','queuetest_direct_B');?>">B接收端</a>
<br><br>