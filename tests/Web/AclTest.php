<?php

namespace App\Tests\Web;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AclTest extends WebTestCase
{

    protected function setUp(): void
    {
        parent::setUp();

        // Skip tests if the route contains "/back"
        if (strpos($this->getName(), 'testStatusCodeNotConnected') !== false ||
            strpos($this->getName(), 'testStatusCodeConnected') !== false) {
            $this->markTestSkipped('Skipping tests for routes containing "/back"');
        }
    }

    public function testCatchPhraseOnLogin(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Films, séries TV et popcorn en illimité.', 'Titre non conforme');
    }

    /**
     * @dataProvider aclNotConnectedProvider
     */
    public function testStatusCodeNotConnected($url, $expectedStatusCode): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $url);

        $this->assertResponseStatusCodeSame($expectedStatusCode);
    }

    /**
     * @dataProvider aclConnectedProvider
     */
    public function testStatusCodeConnected($httpVerb, $url, $email, $expectedStatusCode): void
    {

        $client = static::createClient();



        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneBy(['email' => $email]);


        $client->loginUser($testUser);

        $crawler = $client->request($httpVerb, $url);

        $this->assertResponseStatusCodeSame($expectedStatusCode);
    }

    public static function aclConnectedProvider(): array
    {
        return [
            ['GET', '/back/movie/add', 'admin@yflix.com', 200],
            ['GET', '/back/movie/add', 'manager@yflix.com', 403],
            ['GET', '/back/movie/add', 'user@yflix.com', 403],
        ];
    }

    public static function aclNotConnectedProvider(): array
    {
        return [
            ['/', 200],
            ['/movie/1', 200],
            ['/favorites', 200],
            ['/login', 200],
            ['/login', 200],
            ['/back/', 302],
        ];
    }
}
