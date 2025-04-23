<?php

namespace App\Controller;

use App\Entity\Song;
use App\Enums\Status;
use App\Repository\SongRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Exception\JsonException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[\Deprecated(message: "use the new version instead", since: "V2")]
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

    #[Route('api/v1/song/{id}', name: 'update_song', methods: ['PATCH'])]
    public function update(Song $id, Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager): JsonResponse
    {
        $song = $serializer->deserialize($request->getContent(), Song::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $id]);

        $entityManager->persist($song);
        $entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('api/v1/song/{id}', name: 'delete_song', methods: ['DELETE'])]
    public function delete(Song $song, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $hardDelete = $request->toArray()['hardDelete'] ?? false === true;
        } catch (JsonException) {
            $hardDelete = false;
        }

        if ($hardDelete) {
            $entityManager->remove($song);
        } else {
            $song->setStatus(Status::Inactive->value);
            $entityManager->persist($song);
        }

        $entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    // #[Route('api/v1/song/{id}', name: 'delete_song', methods: ['DELETE'])]
    // public function delete(Song $song, Request $request, EntityManagerInterface $entityManager): JsonResponse
    // {
    //     $hardDelete = $request->query->getBoolean('hardDelete', false);

    //     if ($hardDelete) {
    //         $entityManager->remove($song);
    //     } else {
    //         $song->setStatus(Status::Inactive->value);
    //         $entityManager->persist($song);
    //     }

    //     $entityManager->flush();

    //     return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    // }

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
