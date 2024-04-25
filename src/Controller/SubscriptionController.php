<?php

namespace App\Controller;

use App\Entity\Subscription;
use App\Repository\ContactRepository;
use App\Repository\ProductRepository;
use App\Repository\SubscriptionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class SubscriptionController extends AbstractController
{
    /**
     * Cette méthode permet de récupérer l'ensemble des subscriptions par contact.
     */
    #[OA\Response(
        response: 200,
        description: 'retourne la liste des subscriptions par contact',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Subscription::class, groups: ['getSubscription']))
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'contact not found',
    )]
    #[OA\Parameter(
        name: 'idContact',
        description: 'ID du contact',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer'),
    )]
    #[OA\Tag(
        name: 'Subscription',
    )]
    #[Route('api/subscription/{idContact}', name: 'app_subscription', methods: ['GET'])]
    public function getSubscriptionByContact(int $idContact, SerializerInterface $serializer, SubscriptionRepository $subscriptionRepository): JsonResponse
    {
        $listSubscription = $subscriptionRepository->findByContact($idContact);

        if ($listSubscription) {
            $jsonListSubscription = $serializer->serialize($listSubscription, 'json', ['groups' => 'getSubscription']);

            return new JsonResponse($jsonListSubscription, Response::HTTP_OK, [], true);
        }

        return new JsonResponse([
            'status' => Response::HTTP_NOT_FOUND,
            'message' => 'contact not found'
        ], Response::HTTP_NOT_FOUND);
    }

    /**
     * Cette méthode permet de créer une subscription.
     */
    #[OA\Response(
        response: 201,
        description: 'Opération réussie',
    )]
    #[OA\Response(
        response: 500,
        description: 'Error: Internal Server Error',
    )]
    #[OA\RequestBody(
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'contact', type: 'integer'),
                new OA\Property(property: 'product', type: 'integer'),
                new OA\Property(property: 'begineDate', type: 'datetime'),
                new OA\Property(property: 'endDate', type: 'datetime'),
                ],
            type: 'object'
        ),
    )]
    #[OA\Tag(
        name: 'Subscription',
    )]
    #[Route('api/subscription', name: "createSubscription", methods: ['POST'])]
    public function createSubscription(Request                $request, SerializerInterface $serializer,
                                       EntityManagerInterface $em, ContactRepository $contactRepository,
                                       ProductRepository      $productRepository, ValidatorInterface $validator): JsonResponse
    {
        $subscription = $serializer->deserialize($request->getContent(), Subscription::class, 'json');
        $errors = $validator->validate($subscription);
        if ($errors->count() > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }
        $content = $request->toArray();

        // Récupération de l'idContact. S'il n'est pas défini, alors on met -1 par défaut.
        $idContact = $content['contact'] ?? -1;

        // Récupération de l'idProduct. S'il n'est pas défini, alors on met -1 par défaut.
        $idProduct = $content['product'] ?? -1;

        $subscription->setContact($contactRepository->find($idContact));
        $subscription->setProduct($productRepository->find($idProduct));


        $em->persist($subscription);
        $em->flush();

        $jsonSubscription = $serializer->serialize($subscription, 'json', ['groups' => 'getSubscription']);

        return new JsonResponse($jsonSubscription, Response::HTTP_CREATED, [], true);
    }

    /**
     * Cette méthode permet de modifier une subscription.
     */
    #[OA\Response(
        response: 204,
        description: 'modification avec succès',
    )]
    #[OA\Response(
        response: 500,
        description: 'Error: Internal Server Error',
    )]
    #[OA\RequestBody(
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'contact', type: 'integer'),
                new OA\Property(property: 'product', type: 'integer'),
                new OA\Property(property: 'begineDate', type: 'datetime'),
                new OA\Property(property: 'endDate', type: 'datetime'),
            ],
            type: 'object'
        ),
    )]
    #[OA\Parameter(
        name: 'idSubscription',
        description: 'ID du subscription',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer'),
    )]
    #[OA\Tag(
        name: 'Subscription',
    )]
    #[Route('api/subscription/{idSubscription}', name: "updateSubscription", methods: ['PUT'])]
    public function updateSubscription(int                    $idSubscription, Request $request, SerializerInterface $serializer,
                                       SubscriptionRepository $subscriptionRepository, EntityManagerInterface $em,
                                       ContactRepository      $contactRepository, ProductRepository $productRepository,
                                       ValidatorInterface     $validator): JsonResponse
    {
        $currentSubscription = $subscriptionRepository->find($idSubscription);
        $updatedSubscription = $serializer->deserialize($request->getContent(),
            Subscription::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $currentSubscription]);

        $errors = $validator->validate($updatedSubscription);
        if ($errors->count() > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }

        $content = $request->toArray();

        $idContact = $content['contact'] ?? -1;
        $idProduct = $content['product'] ?? -1;
        $updatedSubscription->setContact($contactRepository->find($idContact));
        $updatedSubscription->setProduct($productRepository->find($idProduct));

        $em->persist($updatedSubscription);
        $em->flush();
        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    /**
     * Cette méthode permet de supprimer une subscription.
     */
    #[OA\Response(
        response: 204,
        description: 'Suppression avec succès',
    )]
    #[OA\Response(
        response: 404,
        description: 'Subscription not found',
    )]
    #[OA\Parameter(
        name: 'idSubscription',
        description: 'ID du subscription',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer'),
    )]
    #[OA\Tag(
        name: 'Subscription',
    )]
    #[Route('api/subscription/{idSubscription}', name: 'deleteSubscription', methods: ['DELETE'])]
    public function deleteSubscription(int $idSubscription, EntityManagerInterface $em, SubscriptionRepository $subscriptionRepository): JsonResponse
    {
        $subscription = $subscriptionRepository->find($idSubscription);

        if ($subscription) {
            $em->remove($subscription);
            $em->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse([
            'status' => Response::HTTP_NOT_FOUND,
            'message' => 'Subscription not found'
        ], Response::HTTP_NOT_FOUND);
    }
}
