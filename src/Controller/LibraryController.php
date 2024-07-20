<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LibraryController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(UserRepository $userRepository, BookRepository $bookRepository): Response
    {
        $users = $userRepository->findAll();
        $books = $bookRepository->findAll();

        return $this->render('library/index.html.twig', [
            'users' => $users,
            'books' => $books,
        ]);
    }
}