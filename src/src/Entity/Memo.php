<?php

namespace App\Entity;

use App\Repository\MemoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MemoRepository::class)]
class Memo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?string $content = null;

    #[ORM\Column(options: ["default" => 0])]
    private int $priority = 0;

    public const STATUS_NOT_STARTED = 0;
    public const STATUS_IN_PROGRESS = 1;
    public const STATUS_DONE = 2;

    public static array $statusLabels = [
        self::STATUS_NOT_STARTED => '着手前',
        self::STATUS_IN_PROGRESS => '進行中',
        self::STATUS_DONE => '完了',
    ];

    #[ORM\Column(options: ["default" => 0])]
    private int $status = self::STATUS_NOT_STARTED;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getPriority(): ?int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): static
    {
        $this->priority = $priority;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): static
    {
        $this->status = $status;

        return $this;
    }
}
