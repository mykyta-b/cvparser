<?php
declare(strict_types=1);

namespace App\Service;

use App\DTO\RepoListDTO;
use App\DTO\ResumeDTO;
use App\DTO\UserDTO;
use App\Service\Gateway\UserApiGateway;
use App\Service\Gateway\ReposApiGateway;

class ResumeParser
{
    /**
     * @var UserApiGateway
     */
    private UserApiGateway $userApi;

    /**
     * @var ReposApiGateway
     */
    private ReposApiGateway $reposApi;


    public function __construct(
        UserApiGateway $userApi,
        ReposApiGateway $repoApi
    ) {
        $this->userApi = $userApi;
        $this->reposApi = $repoApi;
    }

    public function parseUserResume(string $login): ResumeDTO
    {
        $user  = $this->userApi->getByLogin($login);
        $repos = $this->reposApi->getByLogin($login);

        $errors = $this->getErrors($user, $repos);

        if (!empty($errors)) {
            return (new ResumeDTO())
                ->setErrors($errors);
        }

        return (new ResumeDTO())
            ->setUser($user)
            ->setRepos($repos);
    }

    private function getErrors(UserDTO $user, RepoListDTO $repos): array
    {
        $errors = [];
        if (!empty($user->getMessage())) {
            $errors[] = $user->getMessage();
        }
        if (!empty($repos->getMessage())) {
            $errors[] = $repos->getMessage();
        }
        return $errors;
    }
}
