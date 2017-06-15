
<br><br>
工作队列（一个队列多个消费者）
<br><br>
<a target="_blank" href="<?php echo adminUrl('dashboard','lockA');?>">发送消息</a>
<br><br>
<a target="_blank" href="<?php echo adminUrl('dashboard','lockB');?>">A接收端</a>
<a target="_blank" href="<?php echo adminUrl('dashboard','lockB');?>">B接收端</a>
<br><br>
订阅/广播（生产者会发送消息到所有已知的队列，所有消费者都能接收到同样的消息）
<br><br>
<a target="_blank" href="<?php echo adminUrl('dashboard','lockA');?>">发送广播</a>
<br><br>
<a target="_blank" href="<?php echo adminUrl('dashboard','lockB');?>">A接收端</a>
<a target="_blank" href="<?php echo adminUrl('dashboard','lockB');?>">B接收端</a>
<br><br>
消息路由（生产者发送消息，根据消息的不同路由到不同的队列）
<br><br>
<a target="_blank" href="<?php echo adminUrl('dashboard','lockA');?>">发送消息</a>
<br><br>
<a target="_blank" href="<?php echo adminUrl('dashboard','lockB');?>">A接收端</a>
<a target="_blank" href="<?php echo adminUrl('dashboard','lockB');?>">B接收端</a>
<br><br>
消息路由（TOPIC）（生产者发送消息，根据通配符规则同一条消息发到匹配的队列）
<br><br>
<a target="_blank" href="<?php echo adminUrl('dashboard','lockA');?>">发送消息</a>
<br><br>
<a target="_blank" href="<?php echo adminUrl('dashboard','lockB');?>">A接收端</a>
<a target="_blank" href="<?php echo adminUrl('dashboard','lockB');?>">A-1接收端</a>
<a target="_blank" href="<?php echo adminUrl('dashboard','lockB');?>">B接收端</a>
<a target="_blank" href="<?php echo adminUrl('dashboard','lockB');?>">AB接收端</a>