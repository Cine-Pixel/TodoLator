<?php

namespace App\Entity;

use App\Repository\NoteRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=NoteRepository::class)
 */
class Note
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=500)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=Todo::class, inversedBy="notes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $todoId;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getTodoId(): ?Todo
    {
        return $this->todoId;
    }

    public function setTodoId(?Todo $todoId): self
    {
        $this->todoId = $todoId;

        return $this;
    }
}
