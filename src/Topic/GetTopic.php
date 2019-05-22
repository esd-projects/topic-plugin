<?php
/**
 * Created by PhpStorm.
 * User: administrato
 * Date: 2019/5/22
 * Time: 11:52
 */

namespace ESD\Plugins\Topic;


use ESD\BaseServer\Plugins\Logger\GetLogger;
use ESD\BaseServer\Server\Server;
use ESD\Plugins\ProcessRPC\GetProcessRpc;

trait GetTopic
{
    use GetProcessRpc;
    use GetLogger;
    /**
     * @var TopicConfig
     */
    protected $topicConfig;

    protected function getTopicConfig()
    {
        if ($this->topicConfig == null) {
            $this->topicConfig = Server::$instance->getContainer()->get(TopicConfig::class);
        }
        return $this->topicConfig;
    }

    /**
     * @param $topic
     * @param $uid
     * @return bool
     * @throws \ESD\Plugins\ProcessRPC\ProcessRPCException
     */
    public function hasTopic($topic, $uid)
    {
        if (empty($uid)) {
            $this->warn("uid is empty");
            return false;
        }
        $rpcProxy = $this->callProcessName($this->getTopicConfig()->getProcessName(), Topic::class);
        return $rpcProxy->hasTopic($topic, $uid);
    }

    /**
     * 添加订阅
     * @param $topic
     * @param $uid
     * @throws \ESD\Plugins\ProcessRPC\ProcessRPCException
     */
    public function addSub($topic, $uid)
    {
        if (empty($uid)) {
            $this->warn("uid is empty");
            return;
        }
        $rpcProxy = $this->callProcessName($this->getTopicConfig()->getProcessName(), Topic::class,true);
        $rpcProxy->addSub($topic, $uid);
    }

    /**
     * 移除订阅
     * @param $topic
     * @param $uid
     * @throws \ESD\Plugins\ProcessRPC\ProcessRPCException
     */
    public function removeSub($topic, $uid)
    {
        if (empty($uid)) {
            $this->warn("uid is empty");
            return;
        }
        $rpcProxy = $this->callProcessName($this->getTopicConfig()->getProcessName(), Topic::class,true);
        $rpcProxy->removeSub($topic, $uid);
    }

    /**
     * 清除Uid的订阅
     * @param $uid
     * @throws \ESD\Plugins\ProcessRPC\ProcessRPCException
     */
    public function clearUidSub($uid)
    {
        if (empty($uid)) {
            $this->warn("uid is empty");
            return;
        }
        $rpcProxy = $this->callProcessName($this->getTopicConfig()->getProcessName(), Topic::class,true);
        $rpcProxy->removeSub($uid);
    }

    /**
     * @param $topic
     * @param $data
     * @param array $excludeUidList
     * @throws \ESD\Plugins\ProcessRPC\ProcessRPCException
     */
    public function pub($topic, $data, $excludeUidList = [])
    {
        $rpcProxy = $this->callProcessName($this->getTopicConfig()->getProcessName(), Topic::class,true);
        $rpcProxy->pub($topic, $data, $excludeUidList);
    }
}