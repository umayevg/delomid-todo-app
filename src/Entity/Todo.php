<?php

namespace App\Entity;

use App\Repository\TodoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=TodoRepository::class)
 */
class Todo
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups(groups="todo:read")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(max={30}, maxMessage="Max. 30 chars allowed")
     * @Groups(groups="todo:read")
     */
    private $todoname;

    /**
     * @ORM\OneToMany(targetEntity=Task::class, mappedBy="todo", orphanRemoval=true)
     * @Groups(groups="todo:read")
     */
    private $tasks;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="todos")
     * @ORM\JoinColumn(nullable=false)
     * @Groups(groups="todo:read")
     */
    private $user;

    public function __construct()
    {
        $this->tasks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTodoname(): ?string
    {
        return $this->todoname;
    }

    public function setTodoname(string $todoname): self
    {
        $this->todoname = $todoname;

        return $this;
    }

    /**
     * @return Collection|Task[]
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(Task $task): self
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks[] = $task;
            $task->setTodo($this);
        }

        return $this;
    }

    public function removeTask(Task $task): self
    {
        if ($this->tasks->removeElement($task)) {
            // set the owning side to null (unless already changed)
            if ($task->getTodo() === $this) {
                $task->setTodo(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }


    public  function __toString(): string
    {
        return $this->todoname;
    }
}
