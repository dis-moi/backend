<?php

namespace Tests\e2e;

class GetContributorsTest extends BaseApiE2eTestCase
{
    public function testGetContributors()
    {
        $payload = $this->makeApiRequest('/api/v3/contributors');

        $this->assertEquals(2, count($payload));
        $this->assertEquals('John Doe', $payload[0]['name']);
        $this->assertEquals('I’m all out of bubble gum.', $payload[0]['intro']);
        $this->assertEquals('http://localhost/uploads/avatars/photo-fake.jpg', $payload[0]['avatar']);
        $this->assertEquals('Contributor 2', $payload[1]['name']);
    }

    public function testGetContributorsCount()
    {
        $payload = $this->makeApiRequest('/api/v3/contributors');

        $this->assertEquals(2, $payload[0]['contributions']); // 2 public + 1 private
        $this->assertEquals(3, $payload[1]['contributions']); // 3 public
    }

}
