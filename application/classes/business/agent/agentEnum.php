<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');
/*
 * 行长列表逻辑
 */
class  business_agent_agentEnum extends Business {
		/*
		 * 业务员列表
		 */
		public function enumAgent($agent_id=''){
			$agentList=\Core::cache()->get('agents');
			if(!$agentList){
				$agentDao=\Core::dao('user_agent');
			    $agentList=$agentDao->getAgentList();
				if($agentList){
					\Core::cache()->set('agents',$agentList);
				}
			}
			
			return $agent_id?(\Core::arrayKeyExists($agent_id, $agentList)?\Core::arrayGet(\Core::arrayGet($agentList, $agent_id),'real_name'):''):$agentList;
		}
}