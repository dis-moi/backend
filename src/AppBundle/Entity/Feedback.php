<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * Feedback
 *
 * @ORM\Table(name="feedback")
 * @ORM\Entity()
 */
class Feedback
{

    const APPROVE = 'approve';
    const DISMISS = 'dismiss';
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
     * @ORM\ManyToOne(targetEntity="Recommendation", inversedBy="feedbacks")
     */
    private $recommendation;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=100)
     */
    private $type;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="context_datetime", type="datetime", nullable=true)
     */
    private $contextDatetime;

    /**
     * @var string
     *
     * @ORM\Column(name="context_url", type="string", length=1000, nullable=true)
     */
    private $contextUrl;

    /**
     * Alternative constructor.
     *
     * @param Recommendation $recommendation
     * @param string         $type
     * @param array         $context
     */
   public function __construct(Recommendation $recommendation, $type, array $context)
   {
       $this->recommendation = $recommendation;
       $this->setType($type);
       $this->setDatetime($context);
       $this->contextUrl = array_key_exists('url', $context) ? $context['url'] : null;
   }

   public function getType()
   {
       return $this->type;
   }

   public function getContext()
   {
       return new FeedbackContext($this->contextDatetime, $this->contextUrl);
   }

    /**
     * @param $type
     */
    private function setType($type)
    {
        if (!in_array($type, [self::APPROVE, self::DISMISS, self::REPORT])) {
            throw new \InvalidArgumentException(sprintf('Invalid value given for feedback type : %s', $type));
        }
        $this->type = $type;
    }

    /**
     * @param array $context
     */
    private function setDatetime(array $context)
    {
        if (array_key_exists('datetime', $context)) {
            if (\DateTime::createFromFormat(\DateTime::ISO8601, $context['datetime']) === false) {
                $message = sprintf('Invalid feedback context date given : %s', $context['datetime']);
                $message .= 'Expected format : 2016-12-07T12:11:02+00:00';
                throw new \InvalidArgumentException($message);
            }

            $this->contextDatetime = new \DateTime($context['datetime']);
        }
    }
}
