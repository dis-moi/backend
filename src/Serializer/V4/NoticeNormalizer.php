<?php

declare(strict_types=1);

namespace App\Serializer\V4;

use App\Domain\Service\MessagePresenter;
use App\Domain\Service\NoticeUrlGenerator;
use App\Entity\Notice;
use App\Serializer\V3\EntityWithImageNormalizer;
use App\Serializer\V3\NormalizerOptions;
use App\Serializer\V4\Ability\Versioning;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class NoticeNormalizer
    extends EntityWithImageNormalizer
    implements
        ContextAwareNormalizerInterface,
//        ContextAwareDenormalizerInterface,
        NormalizerAwareInterface
{

    use Versioning;

    /**
     * @var NormalizerInterface
     */
    protected $normalizer;

    /**
     * @var NoticeUrlGenerator
     */
    protected $noticeUrlGenerator;

    /**
     * @var MessagePresenter
     */
    private $messagePresenter;

    public function __construct(
        NoticeUrlGenerator $noticeUrlGenerator,
        MessagePresenter $messagePresenter,
        UploaderHelper $uploader,
        RequestStack $requestStack
    ) {
        parent::__construct($uploader, $requestStack);
        $this->noticeUrlGenerator = $noticeUrlGenerator;
        $this->messagePresenter = $messagePresenter;
    }

    /**
     * Sets the owning Normalizer object.
     */
    public function setNormalizer(NormalizerInterface $normalizer): void
    {
        $this->normalizer = $normalizer;
    }

    /**
     * @param mixed $data
     * @param string $format
     * @param mixed[] $context
     * @return bool
     */
    public function supportsNormalization($data, $format = null, $context = []): bool
    {
        $skip = $context['skip_notice_normalizer'] ?? false;
        return $data instanceof Notice && $this->isForV4($context) && ! $skip;
    }

    /**
     * @param mixed[] $context
     * @param mixed|null $format
     * @param mixed $notice
     *
     * @return mixed[]
     * @throws InvalidArgumentException
     * @throws ExceptionInterface
     */
    public function normalize($notice, $format = null, array $context = []): array
    {
        if ( ! ($notice instanceof Notice)) {
            throw new InvalidArgumentException();
        }

        $context['skip_notice_normalizer'] = true;
        $base = $this->normalizer->normalize($notice, $format, $context);
        $extra = [
            'visibility' => $notice->getVisibility()->getValue(),
            'message' => $this->messagePresenter->present($notice->getMessage()),
            'strippedMessage' => $this->messagePresenter->strip($notice->getMessage()),
            'screenshot' => $this->getImageAbsoluteUrl($notice, 'screenshotFile'),
        ];
        return array_merge($base, $extra);

//        'ratings' => [
//            'likes' => $notice->getLikedRatingCount(),
//            'dislikes' => $notice->getDislikedRatingCount(),
//        ],

    }

    public static function formatDateTime(\DateTime $datetime): string
    {
        return $datetime->format('c');
    }

//    /**
//     * {@inheritdoc}
//     *
//     * @param array $context options that denormalizers have access to
//     */
//    public function supportsDenormalization($data, $type, $format = null, array $context = [])
//    {
//        // TODO: Implement supportsDenormalization() method.
//    }
//
//    /**
//     * Denormalizes data back into an object of the given class.
//     *
//     * @param mixed $data Data to restore
//     * @param string $type The expected class to instantiate
//     * @param string $format Format the given data was extracted from
//     * @param array $context Options available to the denormalizer
//     *
//     * @return object|array
//     *
//     * @throws BadMethodCallException   Occurs when the normalizer is not called in an expected context
//     * @throws InvalidArgumentException Occurs when the arguments are not coherent or not supported
//     * @throws UnexpectedValueException Occurs when the item cannot be hydrated with the given data
//     * @throws ExtraAttributesException Occurs when the item doesn't have attribute to receive given data
//     * @throws LogicException           Occurs when the normalizer is not supposed to denormalize
//     * @throws RuntimeException         Occurs if the class cannot be instantiated
//     * @throws ExceptionInterface       Occurs for all the other cases of errors
//     */
//    public function denormalize($data, $type, $format = null, array $context = [])
//    {
//        // TODO: Implement denormalize() method.
//    }
}
