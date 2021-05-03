<?php

declare(strict_types=1);

namespace App\Serializer\V4;

use App\Domain\Service\MessagePresenter;
use App\Entity\Notice;
use App\Serializer\V4\Ability\Normalizing;
use App\Serializer\V4\Ability\Uploading;
use App\Serializer\V4\Ability\Versioning;
use App\Serializer\V4\NormalizerOptions as NormalizerOptionsV4;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;

class NoticeNormalizer implements ContextAwareNormalizerInterface, NormalizerAwareInterface
{
    use Versioning;
    use Normalizing;
    use Uploading;

    /**
     * @var MessagePresenter
     */
    private $messagePresenter;

    public function __construct(
        MessagePresenter $messagePresenter
    ) {
        $this->messagePresenter = $messagePresenter;
    }

    /**
     * @param mixed   $data
     * @param string  $format
     * @param mixed[] $context
     */
    public function supportsNormalization($data, $format = null, $context = []): bool
    {
        $skip = $context[NormalizerOptionsV4::SKIP_NOTICE] ?? false;

        return $data instanceof Notice && $this->isForV4($context) && !$skip;
    }

    /**
     * @param mixed[]    $context
     * @param mixed|null $format
     * @param mixed      $notice
     *
     * @throws InvalidArgumentException
     * @throws ExceptionInterface
     *
     * @return mixed[]
     */
    public function normalize($notice, $format = null, array $context = []): array
    {
        if ( ! ($notice instanceof Notice)) {
            throw new InvalidArgumentException();
        }

        $context[NormalizerOptionsV4::SKIP_NOTICE] = true;
        $base = $this->normalizer->normalize($notice, $format, $context);
        $extra = [
            'visibility' => $notice->getVisibility()->getValue(),
            'message' => $this->messagePresenter->present($notice->getMessage()),
            'strippedMessage' => $this->messagePresenter->strip($notice->getMessage()),
            'screenshot' => $this->getImageAbsoluteUrl($notice, 'screenshotFile'),
        ];

        return array_merge($base, $extra);
    }
}
