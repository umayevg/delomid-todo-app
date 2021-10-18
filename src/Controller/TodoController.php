<?php

namespace App\Controller;

use App\Entity\Todo;
use App\Form\SearchFormType;
use App\Form\TodoType;
use App\Repository\TodoRepository;
use App\SearchFormData\SearchFormData;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class TodoController extends AbstractController
{
    #[Route('/', name: 'todo_index', methods: ['GET'])]
    public function index(TodoRepository $todoRepository, Request $request): Response
    {
        $searchData = new SearchFormData();
        $form = $this->createForm(SearchFormType::class, $searchData);

        $form->handleRequest($request);

        $todos = $todoRepository->getSearchQuery($searchData);

        return $this->render('todo/index.html.twig', [
            'todos' => $todos,
            'form' => $form->createView()
        ]);
    }

    #[Route('/profile/todo/new', name: 'todo_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $todo = new Todo();
        $form = $this->createForm(TodoType::class, $todo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $todo->setUser($this->getUser());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($todo);
            $entityManager->flush();

            return $this->redirectToRoute('todo_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('todo/new.html.twig', [
            'todo' => $todo,
            'form' => $form,
        ]);
    }


    #[Route('/profile/todo/{id}/edit', name: 'todo_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Todo $todo): Response
    {
        // is author of todo?
        if ($this->getUser()->getId() !== $todo->getUser()->getId())
            return $this->redirectToRoute('todo_index', [], Response::HTTP_SEE_OTHER);

        $form = $this->createForm(TodoType::class, $todo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('todo_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('todo/edit.html.twig', [
            'todo' => $todo,
            'form' => $form,
        ]);
    }

    #[Route('/profile/todo/{id}', name: 'todo_delete', methods: ['POST'])]
    public function delete(Request $request, Todo $todo): Response
    {
        if ($this->isCsrfTokenValid('delete' . $todo->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($todo);
            $entityManager->flush();
        }

        return $this->redirectToRoute('todo_index', [], Response::HTTP_SEE_OTHER);
    }


    /**
     * Function pour supprimer les Todos en AJAX
     */
    #[Route('/profile/delete-ajax-todo', name: 'todo_ajax_delete', methods: ['POST'])]
    public function deleteTodoAjax(Request $request)
    {
        if ($request->isXmlHttpRequest())
            return $this->json(['success' => 'is xml']);
        $data = json_decode($request->getContent(), true);
        $entityManager = $this->getDoctrine()->getManager();
        $todo = $entityManager->getRepository(Todo::class)->findOneBy(['id' => (int)$data['element']]);

        if ($this->getUser()->getId() !== $todo->getUser()->getId())
            return $this->json(['error' => 'unauthorized to delete'], 401);

        if ($this->isCsrfTokenValid('ajax-todo-delete', $data['_token'])) {
            $entityManager->remove($todo);
            $entityManager->flush();
            return $this->json(['success' => 'successfully deleted']);
        }

        return $this->json(['error' => 'token invalid'], 403);
    }
}
