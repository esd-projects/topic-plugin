<?php
/**
 * Created by PhpStorm.
 * User: 白猫
 * Date: 2019/5/21
 * Time: 15:46
 */

namespace ESD\Plugins\Topic\Aspect;

use ESD\BaseServer\Plugins\Logger\GetLogger;
use ESD\Plugins\Aop\OrderAspect;
use ESD\Plugins\Topic\GetTopic;
use ESD\Plugins\Uid\Aspect\UidAspect;
use Go\Aop\Intercept\MethodInvocation;
use Go\Lang\Annotation\Before;

class TopicAspect extends OrderAspect
{
    use GetLogger;
    use GetTopic;

    public function __construct()
    {
        //要在UidAspect之前执行，不然uid就被清除了
        $this->atBefore(UidAspect::class);
    }

    /**
     * around onTcpReceive
     *
     * @param MethodInvocation $invocation Invocation
     * @throws \Throwable
     * @Before("within(ESD\BaseServer\Server\IServerPort+) && execution(public **->onTcpClose(*))")
     */
    protected function afterTcpClose(MethodInvocation $invocation)
    {
        list($fd, $reactorId) = $invocation->getArguments();
        $this->clearFdSub($fd);
    }

    /**
     * around onTcpReceive
     *
     * @param MethodInvocation $invocation Invocation
     * @throws \Throwable
     * @Before("within(ESD\BaseServer\Server\IServerPort+) && execution(public **->onWsClose(*))")
     */
    protected function afterWsClose(MethodInvocation $invocation)
    {
        list($fd, $reactorId) = $invocation->getArguments();
        $this->clearFdSub($fd);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return "TopicAspect";
    }
}