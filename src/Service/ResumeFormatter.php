<?php
declare(strict_types=1);

namespace App\Service;

use App\DTO\RepoDTO;
use App\DTO\ResumeDTO;
use Psr\Log\LoggerInterface;

class ResumeFormatter
{
    private const GITHUB_URL = 'https://github.com';
    private const MAX_LANGUAGE_NUM = 5;
    private const TABLE_ROW_LENGTH = 3;
    private const TABLE_CELLS_NUM = 9;
    private const DEBUG_NAME = 'ResumeFormatter';
    private const NUMBER_OF_POPULAR_REPO = 5;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function format(ResumeDTO $resumeDTO): array
    {
        $resumePage = [];

        try {
            $resumePage['githubUrl'] = self::GITHUB_URL;
            $resumePage['name'] = $resumeDTO->getUser()->getName();
            $resumePage['login'] = $resumeDTO->getUser()->getLogin();
            $resumePage['header'] = [
                'avatar' => $resumeDTO->getUser()->getAvatar(),
            ];
            $resumePage['website'] = $this->getWebsite($resumeDTO);
            $repos = $resumeDTO->getRepos();
            $resumePage['profile'] = [
                'location' => $resumeDTO->getUser()->getLocation(),
                'sinceYear' => $this->formatYear($resumeDTO),
                'repoNumber' => count($repos->getRepos()),
                'followers' => $resumeDTO->getUser()->getFollowers(),
            ];
            $resumePage['languages'] = [
                'stats' => $this->formatLanguages($repos->getRepos()),
                'rowLength' => self::TABLE_ROW_LENGTH
            ];

            $resumePage['popular'] = [
                'stats' => $this->formatPopular($repos->getRepos()),
            ];

        } catch (\Throwable $e) {
            $message = vsprintf("%s:%s %s", [self::DEBUG_NAME, 'Could not format resume', $e->getMessage()]);
            $this->logger->error($message);
            $resumePage = [ 'error' => 'Data could not be formatted'];
        }

        return $resumePage;
    }

    public function formatErrors(ResumeDTO $resumeDTO): array
    {
        $errors = $resumeDTO->getErrors();
        return array_unique($errors);
    }

    private function formatLanguages(array $repos): array
    {
        if (is_array($repos) and count($repos) === 0) {
            return [];
        }
        $sum = $this->getAllRepoSizeSum($repos);
        $grouppedLanguages = $this->countLanguageRepoSum($repos);
        $languages = $this->getLanguageToRepoSizeRatio($grouppedLanguages, $sum);

        return $this->formatForTableOutput($languages);
    }

    /**
     * @param ResumeDTO $resumeDTO
     * @return string|null
     */
    private function getWebsite(ResumeDTO $resumeDTO): ?string
    {
        $url = $resumeDTO->getUser()->getWebsite();
        if (empty($url)) {
            return $url;
        }
        if (!preg_match("~^https?://~", $url)) {
            $url = "http://$url";
        }
        return $url;
    }

    /**
     * @param ResumeDTO $resumeDTO
     * @return string
     */
    private function formatYear(ResumeDTO $resumeDTO): string
    {
        $reposList = $resumeDTO->getRepos();
        $yearStart = '';
        if (empty($reposList->getRepos())) {
            $created = $resumeDTO->getUser()->getCreated();
            if ($created instanceof \DateTime) {
                $yearStart = $created->format('Y');
            }
        } else {
            $firstRepo = $reposList->getRepos()[0];
            $yearStart = $firstRepo->getCreated()->format('Y');
        }
        return $yearStart;
    }

    /**
     * @param array $repos
     * @return int
     */
    private function getAllRepoSizeSum(array $repos): int
    {
        return array_reduce(
            $repos,
            function ($sum, RepoDTO $repo) {
                if ($repo->getLanguage() === null) {
                    return $sum;
                }
                return $sum += $repo->getSize();
            },
            0
        );
    }

    /**
     * @param array $repos
     * @return array
     */
    private function countLanguageRepoSum(array $repos): array
    {
        return array_reduce(
            $repos,
            function ($aggregation, RepoDTO $repo) {
                if (is_null($repo->getLanguage())) {
                    return $aggregation;
                }
                if (!isset($aggregation[$repo->getLanguage()])) {
                    $aggregation[$repo->getLanguage()] = $repo->getSize();
                } else {
                    $aggregation[$repo->getLanguage()] += $repo->getSize();
                }
                return $aggregation;
            },
            []
        );
    }

    /**
     * @param array $languages
     * @return array[]
     */
    private function formatForTableOutput(array $languages): array
    {
        $formattedLanguages = [];
        $index = 1;
        for ($i = $index; $i <= self::TABLE_CELLS_NUM; $i++) {
            $lang = current($languages);
            if (empty($lang)) {
                $lang = [];
            }

            $formattedLanguages[$index] = $lang;
            $index += self::TABLE_ROW_LENGTH;
            $index = $index > self::TABLE_CELLS_NUM ? $index - self::TABLE_CELLS_NUM + 1 : $index;
            next($languages);
        }

        ksort($formattedLanguages);
        reset($formattedLanguages);
        return $formattedLanguages;
    }

    /**
     * @param array $grouppedLanguages
     * @param int $sum
     * @return array[]
     */
    private function getLanguageToRepoSizeRatio(array $grouppedLanguages, int $sum): array
    {
        $languages = [];

        arsort($grouppedLanguages);
        foreach ($grouppedLanguages as $language => $size) {
            $languages[] = [
                'percentage' => (int)($size * 100 / $sum),
                'name' => $language
            ];
            if (count($languages) >= self::MAX_LANGUAGE_NUM) {
                break;
            }
        }

        reset($languages);
        return $languages;
    }

    /**
     * @param array $repos ReposDTO[]
     * @return array
     */
    private function formatPopular(array $repos): array
    {
        usort($repos, function (RepoDTO $a, RepoDTO $b) {
            $sumA = (int)$a->getWatchers() + (int)$a->getForks();
            $sumB = (int)$b->getWatchers() + (int)$b->getForks();
            return $sumB <=> $sumA;
        });

        $popularRepos = array_slice($repos, 0, self::NUMBER_OF_POPULAR_REPO);

        $formattedPopular = [];
        foreach ($popularRepos as $repo) {
            $formattedPopular[] = $repo->toArray();
        }

        return $formattedPopular;
    }
}
