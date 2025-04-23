<?php

namespace App\Controller\Api\V2;

use App\Entity\Song;
use App\Enums\Status;
use App\Repository\PoolRepository;
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
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

#[Route('api/v2/song', name: 'api_v2_song_')]
final class SongController extends AbstractController
{
    const TAG_NAME = 'songsCache';

    #[Route('', name: 'get_all', methods: ['GET'])]
    public function getAll(SongRepository $songRepository, SerializerInterface $serializer, TagAwareCacheInterface $cache): JsonResponse
    {
        $cacheReturn = $cache->get('getAllSongs', function (ItemInterface $item) use ($songRepository, $serializer) {
            $item->tag(self::TAG_NAME);
            $data = $songRepository->findAll();
            $jsonData = $serializer->serialize($data, 'json', ['groups' => ['song', 'stats']]);
            return $jsonData;
        });

        return new JsonResponse($cacheReturn, Response::HTTP_OK, [], true);
    }

    #[Route('/{id}', name: 'get', methods: ['GET'])]
    public function get(Song $id, SerializerInterface $serializer): JsonResponse
    {
        $jsonData = $serializer->serialize($id, 'json', ['groups' => ['song', 'stats']]);
        return new JsonResponse($jsonData, Response::HTTP_OK, [], true);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request, PoolRepository $poolRepository, UrlGeneratorInterface $urlGenerator, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator): JsonResponse
    {
        $song = $serializer->deserialize($request->getContent(), Song::class, 'json');

        $errors = $validator->validate($song);

        if ($errors->count() > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }

        foreach ($request->toArray()['idPools'] as $idPool) {
            $pool = $poolRepository->find($idPool);
            $song->addPool($pool);
        }

        $entityManager->persist($song);
        $entityManager->flush();

        $jsonData = $serializer->serialize($song, 'json', ['groups' => ['song', 'stats']]);
        $location = $urlGenerator->generate('api_v2_song_get', ['id' => $song->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonData, Response::HTTP_CREATED, ['location' => $location], true);
    }

    #[Route('/{id}', name: 'update', methods: ['PATCH'])]
    public function update(Song $id, Request $request, PoolRepository $poolRepository, SerializerInterface $serializer, EntityManagerInterface $entityManager, TagAwareCacheInterface $cache): JsonResponse
    {
        $song = $serializer->deserialize($request->getContent(), Song::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $id]);

        $song->removePools();
        foreach ($request->toArray()['idPools'] as $idPool) {
            $pool = $poolRepository->find($idPool);
            $song->addPool($pool);
        }

        $entityManager->persist($song);
        $entityManager->flush();

        // TODO: Add cache for 'create' and 'delete'
        $cache->invalidateTags([self::TAG_NAME]);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
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

}
