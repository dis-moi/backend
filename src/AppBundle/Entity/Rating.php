<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Embeddable\Context;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="rating")
 * @ORM\Entity
 */
class Rating
{
    const DISPLAY = 'display';
    const CLICK   = 'click';

    const APPROVE = 'approve';
    const DISMISS = 'dismiss';
    const REPORT  = 'report';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Notice", inversedBy="ratings")
     */
    private $notice;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=100)
     */
    private $type;

    /**
     * @var Context
     *
     * @ORM\Embedded(class=Context::class)
     */
    private $context;

    /**
     * @var string
     *
     * @ORM\Column(name="reason", type="text")
     */
    private $reason;

    /**
     * @param Notice $notice
     * @param string $type
     * @param array $context
     * @param string $reason
     */
   public function __construct(Notice $notice, $type, Context $context, $reason)
   {
       $this->setNotice($notice);
       $this->setType($type);
       $this->setContext($context);
       $this->setReason($reason);
   }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getNotice()
    {
        return $this->notice;
    }

    /**
     * @param mixed $notice
     */
    public function setNotice($notice)
    {
        $this->notice = $notice;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return Context
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @param Context $context
     */
    public function setContext(Context $context)
    {
        $this->context = $context;
    }

    /**
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * @param string $reason
     */
    public function setReason($reason)
    {
        $this->reason = $reason;
    }

    /**
     * @param array $context
     */
    /*private function setDatetime(array $context)
    {
        if (array_key_exists('datetime', $context)) {
            if (\DateTime::createFromFormat(\DateTime::ISO8601, $context['datetime']) === false) {
                $message = sprintf('Invalid rating context date given : %s', $context['datetime']);
                $message .= 'Expected format : 2016-12-07T12:11:02+00:00';
                throw new \InvalidArgumentException($message);
            }

            $this->contextDatetime = new \DateTime($context['datetime']);
        }
    }*/
}
