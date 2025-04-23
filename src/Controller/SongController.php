<?php

namespace App\Controller;

use App\Entity\Song;
use App\Repository\SongRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class SongController extends AbstractController
{
    #[Route('api/v1/song', name: 'get_all_song', methods: ['GET'])]
    public function getAll(SongRepository $songRepository, SerializerInterface $serializer): JsonResponse
    {
        $data = $songRepository->findAll();
        $jsonData = $serializer->serialize($data, 'json');

        return new JsonResponse($jsonData, Response::HTTP_OK, [], true);
    }

    #[Route('api/v1/song/{id}', name: 'get_song', methods: ['GET'])]
    public function get(Song $id, SerializerInterface $serializer): JsonResponse
    {
        $jsonData = $serializer->serialize($id, 'json');
        return new JsonResponse($jsonData, Response::HTTP_OK, [], true);
    }

    #[Route('api/v1/song', name: 'create_song', methods: ['POST'])]
    public function create(Request $request, UrlGeneratorInterface $urlGenerator, SerializerInterface $serializer, EntityManagerInterface $entityManager): JsonResponse
    {
        $song = $serializer->deserialize($request->getContent(), Song::class, 'json');

        $entityManager->persist($song);
        $entityManager->flush();

        $jsonData = $serializer->serialize($song, 'json');
        $location = $urlGenerator->generate('get_song', ['id' => $song->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonData, Response::HTTP_CREATED, ['location' => $location], true);
    }

    // #[Route('api/v1/song/{song}', name: 'get_song', methods: ['GET'])]
    // public function get(Song $song, SongRepository $songRepository, SerializerInterface $serializer): JsonResponse
    // {
    //     // $data = $songRepository->findOneBy([
    //     //     'id' => $song->getId(),
    //     // ]);

    //     $data = $songRepository->find($song);
    //     $jsonData = $serializer->serialize($data, 'json');

    //     return new JsonResponse($jsonData, Response::HTTP_OK, [], true);
    // }
}
