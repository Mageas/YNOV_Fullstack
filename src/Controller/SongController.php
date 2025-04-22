<?php

namespace App\Controller;

use App\Repository\SongRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class SongController extends AbstractController
{
    #[Route('api/v1/song', name: 'get_all_song', methods: ['GET'])]
    public function getAll(SongRepository $songRepository, SerializerInterface $serializer): JsonResponse
    {
        $data = $songRepository->findAll();
        $jsonData = $serializer->serialize($data, 'json');

        return new JsonResponse($jsonData, Response::HTTP_OK, [], true);
    }
}
