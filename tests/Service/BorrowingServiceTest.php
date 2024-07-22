<?php

namespace App\Tests\Service;

use App\Library\Entity\User;
use App\Library\Entity\Book;
use App\Library\Entity\Borrowing;
use App\Library\Repository\UserRepository;
use App\Library\Repository\BookRepository;
use App\Library\Repository\BorrowingRepository;
use App\Service\BorrowingService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class BorrowingServiceTest extends TestCase
{
    private MockObject $userRepository;
    private MockObject $bookRepository;
    private MockObject $entityManager;
    private BorrowingService $borrowingService;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->bookRepository = $this->createMock(BookRepository::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        $this->borrowingService = new BorrowingService(
            $this->userRepository,
            $this->bookRepository,
            $this->entityManager
        );
    }

    public function testBorrowBookSuccess(): void
    {
        $userId = 1;
        $bookId = 2;

        $user = $this->createMock(User::class);
        $book = $this->createMock(Book::class);

        $this->userRepository->expects($this->once())
            ->method('find')
            ->with($userId)
            ->willReturn($user);

        $this->bookRepository->expects($this->once())
            ->method('find')
            ->with($bookId)
            ->willReturn($book);

        $user->expects($this->once())
            ->method('hasBorrowedBook')
            ->with($book)
            ->willReturn(false);

        $user->expects($this->once())
            ->method('borrows')
            ->with($book);

        $this->entityManager->expects($this->once())
            ->method('flush');

        $this->borrowingService->borrowBook($userId, $bookId);
    }

    public function testBorrowBookUserNotFound(): void
    {
        $userId = 1;
        $bookId = 2;

        $this->userRepository->expects($this->once())
            ->method('find')
            ->with($userId)
            ->willReturn(null); // Simulate user not found

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('User or Book not found');

        $this->borrowingService->borrowBook($userId, $bookId);
    }

    public function testBorrowBookBookNotFound(): void
    {
        $userId = 1;
        $bookId = 2;

        $user = $this->createMock(User::class);

        $this->userRepository->expects($this->once())
            ->method('find')
            ->with($userId)
            ->willReturn($user);

        $this->bookRepository->expects($this->once())
            ->method('find')
            ->with($bookId)
            ->willReturn(null);

        $this->entityManager->expects($this->never())
            ->method('flush');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('User or Book not found');

        $this->borrowingService->borrowBook($userId, $bookId);
    }

    public function testBorrowBookAlreadyBorrowed(): void
    {
        $userId = 1;
        $bookId = 2;

        $user = $this->createMock(User::class);
        $book = $this->createMock(Book::class);

        $this->userRepository->expects($this->once())
            ->method('find')
            ->with($userId)
            ->willReturn($user);

        $this->bookRepository->expects($this->once())
            ->method('find')
            ->with($bookId)
            ->willReturn($book);

        $user->expects($this->once())
            ->method('hasBorrowedBook')
            ->with($book)
            ->willReturn(true);

        $this->entityManager->expects($this->never())
            ->method('flush');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Book already borrowed by this user');

        $this->borrowingService->borrowBook($userId, $bookId);
    }

    public function testReturnBookSuccess(): void
    {
        $userId = 1;
        $bookId = 2;

        // Create a mock user
        $user = $this->createMock(User::class);

        // Create a mock book
        $book = $this->createMock(Book::class);
        $book->expects($this->once())
            ->method('setStatus')
            ->with(true);

        // Create a mock borrowing
        $borrowing = $this->createMock(Borrowing::class);
        $borrowing->expects($this->once())
            ->method('getBook')
            ->willReturn($book);
        $borrowing->expects($this->once())
            ->method('getCheckInDate')
            ->willReturn(null);
        $borrowing->expects($this->once())
            ->method('setCheckinDate')
            ->with($this->isInstanceOf(\DateTimeImmutable::class));

        // Mock the collections
        $borrowingsCollection = new ArrayCollection([$borrowing]);
        $user->expects($this->once())
            ->method('getBorrowings')
            ->willReturn($borrowingsCollection);

        // Mock repositories
        $this->userRepository->expects($this->once())
            ->method('find')
            ->with($userId)
            ->willReturn($user);

        $this->bookRepository->expects($this->once())
            ->method('find')
            ->with($bookId)
            ->willReturn($book);

        // Expect the flush to be called once
        $this->entityManager->expects($this->once())
            ->method('flush');

        // Run the service method
        $this->borrowingService->returnBook($userId, $bookId);
    }

    public function testReturnBookUserNotFound(): void
    {
        $userId = 1;
        $bookId = 2;

        $this->userRepository->expects($this->once())
            ->method('find')
            ->with($userId)
            ->willReturn(null);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('User or Book not found');

        $this->borrowingService->returnBook($userId, $bookId);
    }

    public function testReturnBookBookNotFound(): void
    {
        $userId = 1;
        $bookId = 2;

        $user = $this->createMock(User::class);

        $this->userRepository->expects($this->once())
            ->method('find')
            ->with($userId)
            ->willReturn($user);

        $this->bookRepository->expects($this->once())
            ->method('find')
            ->with($bookId)
            ->willReturn(null);

        $this->entityManager->expects($this->never())
            ->method('flush');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('User or Book not found');

        $this->borrowingService->returnBook($userId, $bookId);
    }

    public function testReturnBookNoActiveBorrowing(): void
    {
        $userId = 1;
        $bookId = 2;

        $user = $this->createMock(User::class);
        $book = $this->createMock(Book::class);

        $this->userRepository->expects($this->once())
            ->method('find')
            ->with($userId)
            ->willReturn($user);

        $this->bookRepository->expects($this->once())
            ->method('find')
            ->with($bookId)
            ->willReturn($book);

        $user->expects($this->once())
            ->method('getBorrowings')
            ->willReturn(new ArrayCollection([]));

        $this->entityManager->expects($this->never())
            ->method('flush');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('No active borrowing found for this book and user');

        $this->borrowingService->returnBook($userId, $bookId);
    }
}