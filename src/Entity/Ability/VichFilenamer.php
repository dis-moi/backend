<?php

namespace App\Entity\Ability;

use Exception;

/**
 * See issue #304 .
 *
 * Trait VichFilenamer
 */
trait VichFilenamer
{
    /**
     * Used by `vich_uploader.yaml`.
     * Filenames are generated once, so changing the implementation of this should not cause trouble
     * for existing contributors.
     *
     * @return string A random string of 32 alphanumerical latin characters matching ^[a-zA-Z0-9]{32}$
     *
     * @throws Exception
     */
    public function getGeneratedFilename(): string
    {
        return $this->makeRandomString(32);
    }

    /**
     * From https://stackoverflow.com/questions/4356289/php-random-string-generator/31107425#31107425 .
     * We could use Ramsey's UUIDv4 generator instead of this.
     *
     * Generate a random string, using a cryptographically secure
     * pseudorandom number generator (random_int).
     *
     * @param int    $length     How many characters do we want?
     * @param string $characters A string of all possible characters to select from
     *
     * @return string A string made of $length characters taken at random from from $characters
     *
     * @throws Exception
     */
    private function makeRandomString(
        int $length = 64,
        string $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
    ): string {
        if ($length < 1) {
            throw new \RangeException('Length must be a positive integer');
        }
        $pieces = [];
        $max = mb_strlen($characters, '8bit') - 1;
        for ($i = 0; $i < $length; ++$i) {
            $pieces[] = $characters[random_int(0, $max)];
        }

        return implode('', $pieces);
    }
}
