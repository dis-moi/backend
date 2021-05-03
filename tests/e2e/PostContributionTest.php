<?php

declare(strict_types=1);

namespace App\Tests\e2e;

use App\Entity\Contributor;
use App\Helper\NoticeVisibility;

class PostContributionTest extends BaseApiE2eTestCase
{
    public function testPostContribution(): void
    {
        $content = json_encode([
            'url' => 'https://www.dismoi.io/confidentialite',
            'contributor' => [
                'name' => 'Johan Dufour',
                'email' => 'johan@dismoi.io',
            ],
            'message' => 'I would prefer seeing a markdown explaining the technical perspectives on the matter.',
         ]);

        $this->client->request('POST', '/api/v3/contributions', [], [], [], $content);
        $this->assertEquals(201, $this->client->getResponse()->getStatusCode());

        $notice = json_decode($this->client->getResponse()->getContent());
        $this->assertEquals(NoticeVisibility::DRAFT_VISIBILITY, $notice->visibility);
    }

    public function testPostQuestion(): void
    {
        /** @var Contributor $contributor */
        $contributor = $this->referenceRepository->getReference('john_doe');
        $content = json_encode([
            'url' => 'https://www.dismoi.io/confidentialite',
            'contributor' => [
                'name' => 'Johan Dufour',
                'email' => 'johan@dismoi.io',
            ],
            'message' => 'I would prefer seeing a markdown explaining the technical perspectives on the matter.',
            'toContributorId' => (string) $contributor->getId(),
            'question' => true,
        ]);

        $this->client->request('POST', '/api/v3/contributions', [], [], [], $content);
        $this->assertEquals(201, $this->client->getResponse()->getStatusCode());

        $notice = json_decode($this->client->getResponse()->getContent());
        $this->assertEquals(NoticeVisibility::QUESTION_VISIBILITY, $notice->visibility);
    }

    public function testPostQuestionWithoutQuestionFlag(): void
    {
        /** @var Contributor $contributor */
        $contributor = $this->referenceRepository->getReference('john_doe');
        $content = json_encode([
            'url' => 'https://www.dismoi.io/confidentialite',
            'contributor' => [
                'name' => 'Johan Dufour',
                'email' => 'johan@dismoi.io',
            ],
            'message' => "I'm not explicitly saying it's a question but, yet it is.",
            'toContributorId' => (string) $contributor->getId(),
        ]);

        $this->client->request('POST', '/api/v3/contributions', [], [], [], $content);
        $this->assertEquals(201, $this->client->getResponse()->getStatusCode());

        $notice = json_decode($this->client->getResponse()->getContent());
        $this->assertEquals(NoticeVisibility::QUESTION_VISIBILITY, $notice->visibility);
    }

    public function testPostQuestionToNonexistentContributor(): void
    {
        $content = json_encode([
            'url' => 'https://www.dismoi.io/confidentialite',
            'contributor' => [
                'name' => 'Johan Dufour',
                'email' => 'johan@dismoi.io',
            ],
            'message' => 'I had a question to a non-existent contributor...',
            'toContributorId' => (string) 0xBAADF00D,
            'question' => true,
        ]);

        $this->client->request('POST', '/api/v3/contributions', [], [], [], $content);
        $this->assertEquals(422, $this->client->getResponse()->getStatusCode());
    }

    public function testPostQuestionToNobody(): void
    {
        $content = json_encode([
            'url' => 'https://www.dismoi.io/confidentialite',
            'contributor' => [
                'name' => 'Johan Dufour',
                'email' => 'johan@dismoi.io',
            ],
            'message' => "I don't know whose the best person to answer this but...",
            'question' => true,
        ]);

        $this->client->request('POST', '/api/v3/contributions', [], [], [], $content);
        $this->assertEquals(201, $this->client->getResponse()->getStatusCode());

        $notice = json_decode($this->client->getResponse()->getContent());
        $this->assertEquals(NoticeVisibility::QUESTION_VISIBILITY, $notice->visibility);
    }
}
