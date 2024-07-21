<?php

namespace App\Library\Entity;

use App\Library\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: "users")]
#[ORM\HasLifecycleCallbacks]
class User
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 100)]
    private $name;

    #[ORM\OneToMany(targetEntity: Borrowing::class, mappedBy: "user", cascade: ["persist"])]
    private $borrowings;

    public function __construct()
    {
        $this->borrowings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|Borrowing[]
     */
    public function getBorrowings(): Collection
    {
        return $this->borrowings;
    }

    public function addBorrowing(Borrowing $borrowing): self
    {
        if (!$this->borrowings->contains($borrowing)) {
            $this->borrowings[] = $borrowing;
            $borrowing->setUser($this);
        }

        return $this;
    }

    public function removeBorrowing(Borrowing $borrowing): self
    {
        if ($this->borrowings->removeElement($borrowing)) {
            // set the owning side to null (unless already changed)
            if ($borrowing->getUser() === $this) {
                $borrowing->setUser(null);
            }
        }

        return $this;
    }

    public function borrows(Book $book): void
    {
        if ($this->hasBorrowedBook($book)) {
            throw new \Exception("Book already borrowed by this user");
        }

        if ($this->activeBorrowedBook()->count() >= 5) {
            throw new \Exception("User cannot borrow more than 5 books");
        }

        $borrowing = new Borrowing($this, $book);
        $this->borrowings->add($borrowing);
    }

    public function hasBorrowedBook(Book $book): bool
    {
        foreach ($this->borrowings as $borrowing) {
            if ($borrowing->getBook() === $book && $borrowing->getCheckInDate() === null) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return Collection|Borrowing[]
     */
    public function activeBorrowedBook(): Collection
    {
        return $this->borrowings->filter(
            function (Borrowing $borrowing) {
                return $borrowing->getCheckinDate() === null;
            }
        );
    }
}
