<?php

namespace App\Controller\Api;

use App\Repository\TodoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{

    #[Route('api/todos', methods: ['GET'])]
    public function listing(Request $request, TodoRepository $todoRepository): JsonResponse
    {
        $order = $request->query->get('order') ?? null;
        $search = $request->query->get('search') ?? null;


        $todos = $todoRepository->findAllTodos($search, $order);
        return $this->json(['todos' => $todos], 200, [], ['groups' => 'todo:read']);
    }
}