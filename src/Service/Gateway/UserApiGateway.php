<?php
declare(strict_types=1);

namespace App\Service\Gateway;

use App\DTO\RequestDTO;
use App\DTO\UserDTO;
use App\Service\Gateway\ApiGatewayInterface;
use App\Service\Gateway\Consumer\ApiConsumer;
use App\Tests\Helpers\Traits\UserApiMockTrait;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Serializer\SerializerInterface;

class UserApiGateway implements ApiGatewayInterface
{
    /**
     * @var ApiConsumer
     */
    private ApiConsumer $consumer;

    /**
     * @var SerializerInterface
     */
    private SerializerInterface $serializer;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    private array $config;

    public function __construct(
        ApiConsumer $consumer,
        SerializerInterface $serializer,
        ParameterBagInterface $parameterBag,
        LoggerInterface $logger
    )
    {
        $this->consumer = $consumer;
        $this->serializer = $serializer;
        $this->logger = $logger;
        $this->config = $parameterBag->get('app.parser.api');
    }

    public function getByLogin(string $login): UserDTO
    {
        try {
            $request = $this->createUserRequest($login);
            $jsonResponse = $this->consumer->request($request);
            $user = $this->serializer->deserialize($jsonResponse, UserDTO::class, 'json');
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());
            $user = new UserDTO();
        }
        return $user;
    }

    private function createUserRequest(string $login): RequestDTO
    {
        $url = preg_replace('~<login>~', $login, $this->config['users']);
        return (new RequestDTO())
            ->setUri($url)
            ->setMethod('GET')
            ;
    }
}
