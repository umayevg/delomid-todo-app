<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\Todo;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class TaskController extends AbstractController
{
    #[Route('/profile/task/', name: 'task_index', methods: ['GET'])]
    public function index(TaskRepository $taskRepository): Response
    {
        return $this->render('task/index.html.twig', [
            'tasks' => $taskRepository->findAll(),
        ]);
    }

    #[Route('/profile/task/new', name: 'task_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $todoId = (int)$request->get('todo_id');
            $entityManager = $this->getDoctrine()->getManager();
            $todo = $entityManager->getRepository(Todo::class)->findOneBy(['id' => $todoId]);

            if (!$todo)
                return $this->redirectToRoute('todo_index', [], Response::HTTP_SEE_OTHER);
            $task->setTodo($todo);
            $task->setUser($this->getUser());

            $entityManager->persist($task);
            $entityManager->flush();

            return $this->redirectToRoute('todo_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('task/new.html.twig', [
            'task' => $task,
            'form' => $form,
        ]);
    }


    #[Route('/profile/task/{id}/edit', name: 'task_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Task $task): Response
    {
        // is current user author of task?
        if ($this->getUser()->getId() !== $task->getUser()->getId())
            return $this->redirectToRoute('todo_index', [], Response::HTTP_SEE_OTHER);


        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('task_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('task/edit.html.twig', [
            'task' => $task,
            'form' => $form,
        ]);
    }

    #[Route('/profile/task/{id}', name: 'task_delete', methods: ['POST'])]
    public function delete(Request $request, Task $task): Response
    {
        // is current user author of task?
        if ($this->getUser()->getId() !== $task->getUser()->getId())
            return $this->redirectToRoute('todo_index', [], Response::HTTP_SEE_OTHER);

        if ($this->isCsrfTokenValid('delete' . $task->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($task);
            $entityManager->flush();
        }

        return $this->redirectToRoute('task_index', [], Response::HTTP_SEE_OTHER);
    }



    /**
     * Function pour supprimer les Tasks en AJAX
     */
    #[Route('profile/task-delete-ajax', name: 'js_delete_task', methods: ['POST'])]
    public function deleteTaskAjax(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $entityManager = $this->getDoctrine()->getManager();
        $task = $entityManager->getRepository(Task::class)->findOneBy(['id' => (int)$data['element']]);

        if (!$task)
            return $this->json(['error' => 'task object not found'], 404);


        if ($this->getUser()->getId() !== $task->getUser()->getId())
            return $this->json(['error' => 'unauthorized to delete'], 401);

        if ($this->isCsrfTokenValid('ajax-task-delete', $data['_token'])) {
            $entityManager->remove($task);
            $entityManager->flush();
            return $this->json(['success' => 'successfully deleted']);
        }

        return $this->json(['error' => 'token invalid'], 403);
    }


    /**
     * Function pour changer le status des Task
     */
    #[Route('profile/task-status-change-ajax', name: 'ajax_status_change', methods: ['POST'])]
    public function statusChange(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $entityManager = $this->getDoctrine()->getManager();
        $task = $entityManager->getRepository(Task::class)->findOneBy(['id' => (int)$data['element']]);

        // on verifie si Task existe
        if (!$task)
            return $this->json(['error' => 'task object not found'], 404);

        // on verifie si l'utilisateur est author de la Task ou de la Todo
        if (($this->getUser()->getId() !== $task->getUser()->getId()) && ($this->getUser()->getId() !== $task->getTodo()->getUser()->getId()))
            return $this->json(['error' => 'unauthorized to update status'], 401);

        if ($this->isCsrfTokenValid('task-status', $data['_token'])) {
            $status = $data['status'] == true ? 'completed' : null;
            $task->setStatus($status);
            $entityManager->flush();
            return $this->json(['success' => 'status successfully updated']);
        }

        return $this->json(['error' => 'token invalid'], 403);
    }
}
