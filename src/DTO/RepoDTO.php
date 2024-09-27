<?php
declare(strict_types=1);

namespace App\DTO;

use App\DTO\Helper\DTOTrait;
use Symfony\Component\Serializer\Annotation\SerializedName;

class RepoDTO
{
    use DTOTrait;

    /**
     * @var string|null
     */
    private ?string $name = '';

    /**
     * @var string|null
     */
    private ?string $language = '';

    /**
     * @var int|null
     */
    private ?int $size = 0;

    /**
     * @var \DateTime|null
     * @SerializedName ("created_at")
     */
    private ?\DateTime $created;

    /**
     * @SerializedName ("watchers_count")
     */
    private ?int $watchers = 0;

    /**
     * @SerializedName ("forks_count")
     */
    private ?int $forks = 0;

    /**
     * @var string|null
     */
    private ?string $description = '';

    /**
     * @var int|null
     * @SerializedName ("stargazers_count")
     */
    private ?int $stars = 0;

    /**
     * @var string|null
     * @SerializedName ("html_url")
     */
    private ?string $htmlUrl;

    /**
     * @var string|null
     * @SerializedName ("homepage")
     */
    private ?string $homePage;

    /**
     * @var \DateTime|null
     * @SerializedName ("pushed_at")
     */
    private ?\DateTime $pushed;

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return self
     */
    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLanguage(): ?string
    {
        return $this->language;
    }

    /**
     * @param string|null $language
     * @return RepoDTO
     */
    public function setLanguage(?string $language): self
    {
        $this->language = $language;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getSize(): ?int
    {
        return $this->size;
    }

    /**
     * @param int|null $size
     * @return RepoDTO
     */
    public function setSize(?int $size): self
    {
        $this->size = $size;
        return $this;
    }

    /**
     * @return \DateTime|null
     * @SerializedName ("created_at")
     */
    public function getCreated(): ?\DateTime
    {
        return $this->created;
    }

    /**
     * @param \DateTime|null $created
     * @SerializedName ("created_at")
     * @return RepoDTO
     */
    public function setCreated(?\DateTime $created): self
    {
        $this->created = $created;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getWatchers(): ?int
    {
        return $this->watchers;
    }

    /**
     * @param int|null $watchers
     * @return self
     */
    public function setWatchers(?int $watchers): self
    {
        $this->watchers = $watchers;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getForks(): ?int
    {
        return $this->forks;
    }

    /**
     * @param int|null $forks
     * @return self
     */
    public function setForks(?int $forks): self
    {
        $this->forks = $forks;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     * @return self
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getStars(): ?int
    {
        return $this->stars;
    }

    /**
     * @param int|null $stars
     * @return self
     */
    public function setStars(?int $stars): self
    {
        $this->stars = $stars;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getHtmlUrl(): ?string
    {
        return $this->htmlUrl;
    }

    /**
     * @param string|null $htmlUrl
     * @return self
     */
    public function setHtmlUrl(?string $htmlUrl): self
    {
        $this->htmlUrl = $htmlUrl;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getHomePage(): ?string
    {
        return $this->homePage;
    }

    /**
     * @param string|null $homePage
     * @return self
     */
    public function setHomePage(?string $homePage): self
    {
        $this->homePage = $homePage;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getPushed(): ?\DateTime
    {
        return $this->pushed;
    }

    /**
     * @param \DateTime|null $pushed
     * @return self
     */
    public function setPushed(?\DateTime $pushed): self
    {
        $this->pushed = $pushed;
        return $this;
    }
}

