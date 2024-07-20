<?php

namespace App\Controller\Api;

use App\Entity\Book;
use App\Service\BorrowingService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class BookController extends AbstractController
{
    private $entityManager;

    private $borrowingService;

    public function __construct(EntityManagerInterface $entityManager, BorrowingService $borrowingService)
    {
        $this->entityManager = $entityManager;
        $this->borrowingService = $borrowingService;
    }

    #[Route("/api/books", name: "api_list_books", methods: ["GET"])]
    public function listBooks(): JsonResponse
    {
        $books = $this->entityManager->getRepository(Book::class)->findAll();
        $data = [];

        foreach ($books as $book) {
            $data[] = [
                'id' => $book->getId(),
                'title' => $book->getTitle(),
                'author' => $book->getAuthor(),
                'status' => $book->isAvailable() ? 'Available' : 'Borrowed',
            ];
        }

        return new JsonResponse($data);
    }

    #[Route("/api/borrow", name: "api_borrow", methods: ["POST"])]
    public function borrowBook(Request $request): JsonResponse
    {
        $userId = $request->request->get('user_id');
        $bookId = $request->request->get('book_id');

        try {
            $this->borrowingService->borrowBook($userId, $bookId);
            return new JsonResponse(['status' => 'Book borrowed successfully']);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    #[Route("/api/return", name: "api_return", methods: ["POST"])]
    public function returnBook(Request $request): JsonResponse
    {
        $userId = $request->request->get('user_id');
        $bookId = $request->request->get('book_id');

        try {
            $this->borrowingService->returnBook($userId, $bookId);
            return new JsonResponse(['status' => 'Book returned successfully']);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
}