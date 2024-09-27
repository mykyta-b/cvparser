<?php
declare(strict_types=1);

namespace App\Service\Gateway;

use App\DTO\ErrorDTO;
use App\DTO\RepoDTO;
use App\DTO\RepoListDTO;
use App\DTO\RequestDTO;
use App\Service\Gateway\Consumer\ApiConsumer;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class is intended to parse responses
 * from repositories github api
 * and put them to DTO
 * @see https://docs.github.com/en/rest/reference/repos
 */
class ReposApiGateway
{
    private const REPOS_PER_PAGE = 100;
    private const LAST_PAGE_NUMBER = 20;
    private const START_PAGE_NUMBER = 1;

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

    public function getByLogin(string $login): RepoListDTO
    {
        $repoListDTO = new RepoListDTO();
        try {
            $page = self::START_PAGE_NUMBER;
            do {
                $request = $this->createRequest($login, $page);
                $jsonResponse = $this->consumer->request($request);

                $error = $this->serializer->deserialize($jsonResponse, ErrorDTO::class, 'json');
                if (!empty($error->getMessage())) {
                    $repoListDTO->setMessage($error->getMessage());
                    throw new RequestErrorException($error->getMessage());
                }

                $repos = $this->serializer->deserialize($jsonResponse, RepoDTO::class . '[]', 'json');
                $repoListDTO->addRepos($repos);

            } while ($page++ < self::LAST_PAGE_NUMBER and count($repos) == self::REPOS_PER_PAGE);
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());
            $repoListDTO->setMessage($e->getMessage());
        }

        return $repoListDTO;
    }

    private function createRequest(string $login, ?int $page = null): RequestDTO
    {
        $url = preg_replace('~<login>~', $login, $this->config['repos']);
        $url .= "&per_page=" . self::REPOS_PER_PAGE;
        if ($page !== null) {
            $url .= '&page=' . $page;
        }
        return (new RequestDTO())
            ->setUri($url)
            ->setMethod('GET')
            ;
    }
}
