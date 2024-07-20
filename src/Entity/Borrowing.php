<?php

namespace App\Entity;

use App\Repository\BorrowingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BorrowingRepository::class)]
#[ORM\Table(name: "borrowings")]
#[ORM\HasLifecycleCallbacks]
class Borrowing
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'borrowings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Book::class, inversedBy: 'borrowings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Book $book = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $checkoutDate = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $checkInDate = null;

    public function __construct(User $user, Book $book)
    {
        $this->user = $user;
        $this->book = $book;
        $this->checkoutDate = new \DateTimeImmutable();
        $this->checkInDate = null;
    }

    public function __toString()
    {
        return sprintf('Borrowing { id: %d, user: %s, book: %s }', $this->id, $this->user, $this->book);
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getBook(): ?Book
    {
        return $this->book;
    }

    public function setBook(?Book $book): self
    {
        $this->book = $book;
        return $this;
    }


    public function getCheckoutDate(): ?\DateTimeInterface
    {
        return $this->checkoutDate;
    }

    public function setCheckoutDate(\DateTimeInterface $checkoutDate): self
    {
        $this->checkoutDate = $checkoutDate;
        return $this;
    }

    public function getCheckinDate(): ?\DateTimeInterface
    {
        return $this->checkInDate;
    }

    public function setCheckinDate(?\DateTimeInterface $checkInDate): self
    {
        $this->checkInDate = $checkInDate;
        return $this;
    }
}
