<?php

namespace App\Tests\e2e;

use App\Entity\Contributor;
use App\Helper\NoticeVisibility;

class PostContributionTest extends BaseApiE2eTestCase
{
    public function testPostContribution()
    {
        $content = json_encode([
            'url' => 'https://www.dismoi.io/confidentialite',
            'contributor' => [
                'name' => 'Johan Dufour',
                'email' => 'johan@dismoi.io',
             ],
            'message' => 'I would prefer seeing a markdown explaining the technical perspectives on the matter.',
            'toContributorId' => null,
         ]);

        $this->client->request('POST', '/api/v3/contributions', [], [], [], $content);
        $this->assertEquals(201, $this->client->getResponse()->getStatusCode());

        $notice = json_decode($this->client->getResponse()->getContent());
        $this->assertEquals(NoticeVisibility::DRAFT_VISIBILITY, $notice->visibility, 'this is a message');
    }

    public function testPostQuestion()
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
            'toContributorId' => $contributor->getId(),
        ]);

        $this->client->request('POST', '/api/v3/contributions', [], [], [], $content);
        $this->assertEquals(201, $this->client->getResponse()->getStatusCode());

        $notice = json_decode($this->client->getResponse()->getContent());
        $this->assertEquals(NoticeVisibility::QUESTION_VISIBILITY, $notice->visibility, 'this is a message');
    }

    public function testPostQuestionToNonexistentContributor()
    {
        $content = json_encode([
            'url' => 'https://www.dismoi.io/confidentialite',
            'contributor' => [
                'name' => 'Johan Dufour',
                'email' => 'johan@dismoi.io',
            ],
            'message' => 'I would prefer seeing a markdown explaining the technical perspectives on such matter.',
            'toContributorId' => 0xBAADF00D,
        ]);

        $this->client->request('POST', '/api/v3/contributions', [], [], [], $content);
        $this->assertEquals(422, $this->client->getResponse()->getStatusCode());
    }
}
