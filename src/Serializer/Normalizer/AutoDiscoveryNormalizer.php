<?php

namespace App\Serializer\Normalizer;

use App\Entity\Song;
use ReflectionClass;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class AutoDiscoveryNormalizer implements NormalizerInterface
{
    public function __construct(
        #[Autowire(service: 'serializer.normalizer.object')]
        private NormalizerInterface $normalizer,
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    // TODO: Regarder pour utiliser le 'Route' du controller
    // Ajouter dans le contexte v1, v2, ...
    public function normalize($object, ?string $format = null, array $context = []): array
    {
        $data = $this->normalizer->normalize($object, $format, $context);

        $className = (new ReflectionClass($object))->getShortName();
        $className = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $className));

        $version = $context['version'] ?? 'v1';

        $data['_links'] = [
            'self' => [
                'href' => $this->urlGenerator->generate("api_{$version}_{$className}_get", [
                    'id' => $data['id'],
                ]),
                'methods' => ['GET'],
            ],
            'up' => [
                'href' => $this->urlGenerator->generate("api_{$version}_{$className}_get_all"),
                'methods' => ['GET'],
            ],
            // TODO : Ajouter les autres methodes pour finir le CRUD
        ];

        return $data;
    }

    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        return ($data instanceof Song) && $format === 'json';
    }

    public function getSupportedTypes(?string $format): array
    {
        return [Song::class => true];
    }
}
