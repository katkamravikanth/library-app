<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Borrowing;
use App\Repository\BookRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    #[Route('/books', name: 'app_book_list')]
    public function list(BookRepository $bookRepository): Response
    {
        $books = $bookRepository->findAll();

        return $this->render('book/list.html.twig', [
            'books' => $books,
        ]);
    }

    #[Route('/books/{id}', name: 'app_book_detail')]
    public function detail(Book $book, UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        return $this->render('book/detail.html.twig', [
            'book' => $book,
            'users' => $users,
        ]);
    }

    #[Route('/books/{id}/borrow', name: 'app_book_borrow', methods: ['POST'])]
    public function borrow(Book $book, Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        $userId = $request->request->get('user_id');
        $user = $userRepository->find($userId);

        if ($book->isAvailable()) {
            $borrowing = new Borrowing($user, $book);
            $entityManager->persist($borrowing);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_book_detail', ['id' => $book->getId()]);
    }

    #[Route('/books/{id}/return', name: 'app_book_return', methods: ['POST'])]
    public function return(Book $book, EntityManagerInterface $entityManager): Response
    {
        $borrowing = $book->getBorrowings()->last();
        if ($borrowing && !$borrowing->getCheckinDate()) {
            $borrowing->setCheckinDate(new \DateTimeImmutable());
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_book_detail', ['id' => $book->getId()]);
    }
}