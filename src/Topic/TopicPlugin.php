<?php
/**
 * Created by PhpStorm.
 * User: 白猫
 * Date: 2019/5/22
 * Time: 10:13
 */

namespace ESD\Plugins\Topic;

use ESD\BaseServer\Memory\CrossProcess\Table;
use ESD\BaseServer\Server\Context;
use ESD\BaseServer\Server\PlugIn\AbstractPlugin;
use ESD\BaseServer\Server\PlugIn\PluginInterfaceManager;
use ESD\BaseServer\Server\Server;
use ESD\Plugins\Aop\AopConfig;
use ESD\Plugins\Topic\Aspect\TopicAspect;
use ESD\Plugins\Uid\UidConfig;
use ESD\Plugins\Uid\UidPlugin;

class TopicPlugin extends AbstractPlugin
{
    const processGroupName = "HelperGroup";
    /**
     * @var Table
     */
    protected $topicTable;
    /**
     * @var TopicConfig
     */
    private $topicConfig;
    /**
     * @var Topic
     */
    private $topic;
    /**
     * @var TopicAspect
     */
    private $topicAspect;

    /**
     * TopicPlugin constructor.
     * @param TopicConfig|null $topicConfig
     * @throws \ReflectionException
     * @throws \DI\DependencyException
     */
    public function __construct(?TopicConfig $topicConfig = null)
    {
        parent::__construct();
        if ($topicConfig == null) $topicConfig = new TopicConfig();
        $this->topicConfig = $topicConfig;
        $this->atAfter(UidPlugin::class);
    }

    /**
     * @param PluginInterfaceManager $pluginInterfaceManager
     * @return mixed|void
     * @throws \DI\DependencyException
     * @throws \ESD\BaseServer\Exception
     * @throws \ReflectionException
     */
    public function onAdded(PluginInterfaceManager $pluginInterfaceManager)
    {
        parent::onAdded($pluginInterfaceManager);
        $pluginInterfaceManager->addPlug(new UidPlugin());
    }

    /**
     * @param Context $context
     * @return mixed|void
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    public function init(Context $context)
    {
        parent::init($context);
        $aopConfig = Server::$instance->getContainer()->get(AopConfig::class);
        $this->topicAspect = new TopicAspect();
        $aopConfig->addAspect($this->topicAspect);
    }

    /**
     * 获取插件名字
     * @return string
     */
    public function getName(): string
    {
        return "Topic";
    }

    /**
     * 初始化
     * @param Context $context
     * @return mixed
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \ESD\BaseServer\Server\Exception\ConfigException
     * @throws \ReflectionException
     */
    public function beforeServerStart(Context $context)
    {
        $this->topicConfig->merge();
        $uidConfig = Server::$instance->getContainer()->get(UidConfig::class);
        $this->topicTable = new Table($this->topicConfig->getCacheTopicCount());
        $this->topicTable->column("topic", Table::TYPE_STRING, 65535);
        $this->topicTable->column("uid", Table::TYPE_STRING, $uidConfig->getUidMaxLength());
        $this->topicTable->create();
        //添加一个TopicProcess进程
        $context->getServer()->addProcess($this->topicConfig->getProcessName(), TopicProcess::class, self::processGroupName);
        return;
    }

    /**
     * 在进程启动前
     * @param Context $context
     * @return mixed
     */
    public function beforeProcessStart(Context $context)
    {
        if (Server::$instance->getProcessManager()->getCurrentProcess()->getProcessName() == $this->topicConfig->getProcessName()) {
            //topic进程
            $this->topic = new Topic($this->topicTable);
            $this->setToDIContainer(Topic::class, $this->topic);
        }
        $this->ready();
    }
}