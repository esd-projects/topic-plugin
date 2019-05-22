<?php
/**
 * Created by PhpStorm.
 * User: 白猫
 * Date: 2019/5/22
 * Time: 10:56
 */

namespace ESD\Plugins\Topic;


use ESD\BaseServer\Plugins\Config\BaseConfig;

class TopicConfig extends BaseConfig
{
    const key = "topic";
    /**
     * @var int
     */
    protected $cacheTopicCount = 100000;
    /**
     * 默认在helper进程，可设置其他名字，将新建进程
     * @var string
     */
    protected $processName = "helper";
    public function __construct()
    {
        parent::__construct(self::key);
    }

    /**
     * @return int
     */
    public function getCacheTopicCount(): int
    {
        return $this->cacheTopicCount;
    }

    /**
     * @param int $cacheTopicCount
     */
    public function setCacheTopicCount(int $cacheTopicCount): void
    {
        $this->cacheTopicCount = $cacheTopicCount;
    }

    /**
     * @return string
     */
    public function getProcessName(): string
    {
        return $this->processName;
    }

    /**
     * @param string $processName
     */
    public function setProcessName(string $processName): void
    {
        $this->processName = $processName;
    }
}