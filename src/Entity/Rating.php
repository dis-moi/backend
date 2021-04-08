<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Embeddable\Context;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

/**
 * @ORM\Table(name="rating")
 * @ORM\Entity
 */
class Rating
{
    public const BADGE = 'badge';
    public const DISPLAY = 'display';
    public const UNFOLD = 'unfold';
    public const OUTBOUND_CLICK = 'outbound-click';

    public const LIKE = 'like';
    public const UNLIKE = 'unlike';

    public const DISLIKE = 'dislike';
    public const UNDISLIKE = 'undislike';

    public const DISMISS = 'dismiss';
    public const UNDISMISS = 'undismiss';

    public const REPORT = 'report';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Notice
     *
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

    public function __construct(Notice $notice, string $type, Context $context, string $reason)
    {
        $this->setNotice($notice);
        $this->setType($type);
        $this->setContext($context);
        $this->setReason($reason);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getNotice(): Notice
    {
        return $this->notice;
    }

    public function setNotice(Notice $notice): self
    {
        $this->notice = $notice;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        if (!\in_array($type, [
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
        ], true)) {
            throw new InvalidArgumentException(sprintf('Invalid value given for feedback type : %s', $type));
        }
        $this->type = $type;

        return $this;
    }

    public function getContext(): Context
    {
        return $this->context;
    }

    public function setContext(Context $context): self
    {
        $this->context = $context;

        return $this;
    }

    public function getReason(): string
    {
        return $this->reason;
    }

    public function setReason(string $reason): self
    {
        $this->reason = $reason;

        return $this;
    }
}
