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
    const BADGE = 'badge';
    const DISPLAY = 'display';
    const UNFOLD = 'unfold';
    const OUTBOUND_CLICK = 'outbound-click';

    const LIKE = 'like';
    const UNLIKE = 'unlike';

    const DISLIKE = 'dislike';
    const UNDISLIKE = 'undislike';

    const DISMISS = 'dismiss';
    const UNDISMISS = 'undismiss';

    const REPORT = 'report';

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
     * @param string $type
     * @param array  $context
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
        if (!in_array($type, [
            self::DISMISS,
            self::UNDISMISS,
            self::LIKE,
            self::UNLIKE,
            self::DISLIKE,
            self::UNDISLIKE,
            self::BADGE,
            self::DISPLAY,
            self::UNFOLD,
            self::OUTBOUND_CLICK,
            self::REPORT,
        ])) {
            throw new \InvalidArgumentException(sprintf('Invalid value given for feedback type : %s', $type));
        }
        $this->type = $type;
    }

    /**
     * @return Context
     */
    public function getContext()
    {
        return $this->context;
    }

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
}
