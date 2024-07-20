<?php

namespace App\Service;

use App\Repository\UserRepository;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;

class BorrowingService
{
    private $userRepository;
    private $bookRepository;
    private $entityManager;

    public function __construct(UserRepository $userRepository, BookRepository $bookRepository, EntityManagerInterface $entityManager)
    {
        $this->userRepository = $userRepository;
        $this->bookRepository = $bookRepository;
        $this->entityManager = $entityManager;
    }

    public function borrowBook(int $userId, int $bookId): void
    {
        $user = $this->userRepository->find($userId);
        $book = $this->bookRepository->find($bookId);

        if (!$user || !$book) {
            throw new \Exception("User or Book not found");
        }

        // Make sure this logic is in place
        if ($user->hasBorrowedBook($book)) {
            throw new \Exception("Book already borrowed by this user");
        }

        $user->borrows($book);
        $this->entityManager->flush();
    }

    public function returnBook(int $userId, int $bookId): void
    {
        $user = $this->userRepository->find($userId);
        $book = $this->bookRepository->find($bookId);

        if (!$user || !$book) {
            throw new \Exception("User or Book not found");
        }

        foreach ($user->getBorrowings() as $borrowing) {
            if ($borrowing->getBook() === $book && $borrowing->getCheckInDate() === null) {
                $borrowing->setCheckinDate(new \DateTimeImmutable());
                $book->setStatus(true);
                $this->entityManager->flush();
                return;
            }
        }

        throw new \Exception("No active borrowing found for this book and user");
    }
}