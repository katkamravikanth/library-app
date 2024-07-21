<?php

namespace App\Controller;

use App\Library\Entity\Book;
use App\Library\Entity\Borrowing;
use App\Library\Entity\User;
use App\Library\Repository\BookRepository;
use App\Library\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/users', name: 'app_user_list')]
    public function list(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        return $this->render('user/list.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/users/{id}', name: 'app_user_detail')]
    public function detail(User $user, BookRepository $bookRepository): Response
    {
        $availableBooks = $bookRepository->findBy(['status' => true]);

        return $this->render('user/detail.html.twig', [
            'user' => $user,
            'availableBooks' => $availableBooks,
        ]);
    }

    #[Route('/users/{id}/borrow', name: 'app_user_borrow_book', methods: ['POST'])]
    public function borrowBook(User $user, Request $request, BookRepository $bookRepository, EntityManagerInterface $entityManager): Response
    {
        $bookId = $request->request->get('book_id');
        $book = $bookRepository->find($bookId);

        if (!$book) {
            throw $this->createNotFoundException('Book not found');
        }

        if (!$book->isAvailable()) {
            $this->addFlash('error', 'Book is not available');
            return $this->redirectToRoute('app_user_detail', ['id' => $user->getId()]);
        }

        // Check if the user has already borrowed 5 books
        if ($user->activeBorrowedBook()->count() >= 5) {
            $this->addFlash('error', 'You cannot borrow more than 5 books');
            return $this->redirectToRoute('app_user_detail', ['id' => $user->getId()]);
        }

        // Create a new borrowing entry
        $borrowing = new Borrowing($user, $book);
        $entityManager->persist($borrowing);

        // Mark the book as unavailable
        $book->setStatus(false);
        $entityManager->persist($book);

        $entityManager->flush();

        $this->addFlash('success', 'Book borrowed successfully');
        return $this->redirectToRoute('app_user_detail', ['id' => $user->getId()]);
    }

    #[Route('/user/{id}/return', name: 'app_user_book_return', methods: ['POST'])]
    public function return(Book $book, EntityManagerInterface $entityManager): Response
    {
        $borrowing = $book->getBorrowings()->last();
        if ($borrowing && !$borrowing->getCheckinDate()) {
            $borrowing->setCheckinDate(new \DateTimeImmutable());
            $book->setStatus(true);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_detail', ['id' => $borrowing->getUser()->getId()]);
    }
}