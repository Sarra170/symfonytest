<?php

namespace App\Controller;

use App\Entity\Author;
use App\Entity\Book;
use App\Form\BookType;
use App\Form\SearchType;
use App\Repository\BookRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    #[Route('/book', name: 'app_book')]
    public function index(): Response
    {
        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController',
        ]);
    }

    #[Route('/AfficheBook', name: 'app_AfficheBook')]
    public function Affiche(BookRepository $repository, ManagerRegistry $doctrine)
    {
        //récupérer les livres publiés
        $books = $repository->findAll();
        $numPublishedBooks = count($repository->findBy(['published' => true]));
        $numUnPublishedBooks = count($repository->findBy(['published' => false]));
        return $this->render('book/Affiche.html.twig', [
            'books' => $books,
            'numPublishedBooks' => $numPublishedBooks,
            'numUnPublishedBooks' => $numUnPublishedBooks
        ]);

    }

    #[Route('/AddBook', name: 'app_AddBook')]
    public function Add(Request $request, ManagerRegistry $doctrine)
    {
        $book = new Book();
        $form = $this->CreateForm(BookType::class, $book);
        $form->add('Ajouter', SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //initialisation de l'attribut "published" a true
            //  $book->setPublished(true);
            // get the accociated author from the book entity
            $author = $book->getAuthor();
            //incrementation de l'attribut "nb_books" de l'entire Author

            if ($author instanceof Author) {
                $author->setNbBooks($author->getNbBooks() + 1);
            }
            $em = $doctrine->getManager();
            $em->persist($book);
            $em->flush();
            return $this->redirectToRoute('app_AfficheBook');
        }
        return $this->render('book/Add.html.twig', ['f' => $form->createView()]);

    }


    #[Route('/editbook/{ref}', name: 'app_editBook')]
    public function edit(BookRepository $repository, $ref, Request $request, ManagerRegistry $doctrine)
    {
        $author = $repository->find($ref);
        $form = $this->createForm(BookType::class, $author);
        $form->add('Edit', SubmitType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->flush(); // Correction : Utilisez la méthode flush() sur l'EntityManager pour enregistrer les modifications en base de données.
            return $this->redirectToRoute("app_AfficheBook");
        }

        return $this->render('book/edit.html.twig', [
            'f' => $form->createView(),
        ]);
    }


    #[Route('/deletebook/{ref}', name: 'app_deleteBook')]
    public function delete($ref, BookRepository $repository, ManagerRegistry $doctrine)
    {
        $book = $repository->find($ref);


        $em = $doctrine->getManager();
        $em->remove($book);
        $em->flush();


        return $this->redirectToRoute('app_AfficheBook');
    }

    #[Route('/ShowBook/{ref}', name: 'app_detailBook')]
    public function showBook($ref, BookRepository $repository)
    {
        $book = $repository->find($ref);
        if (!$book) {
            return $this->redirectToRoute('app_AfficheBook');
        }

        return $this->render('book/show.html.twig', ['b' => $book]);

    }

    public function triQB(BookRepository $bookRepository): Response
    {
        $books = $bookRepository->triQB();
        $numPublishedBooks = count($bookRepository->findBy(['published' => true]));
        $numUnPublishedBooks = count($bookRepository->findBy(['published' => false]));
        return $this->render('book/Affiche.html.twig', [
            'books' => $books,
            'numPublishedBooks' => $numPublishedBooks,
            'numUnPublishedBooks' => $numUnPublishedBooks
        ]);
    }

    #[Route('/Author/Search', name: 'Search')]
    public function Search(BookRepository $bookRepository, Request $request): Response
    {
        $books = $bookRepository->findAll();
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
            $title = $form->get('title');
            $books = $bookRepository->SearchBooksByTitle($title);
            return $this->render('book/Affiche.html.twig', [
                'form' => $form,
                'books' => $books,

            ]);
        }

        return $this->renderForm('book/Affiche.html.twig', [
            'books' => $books,
            'form' => $form,
        ]);
    }
}