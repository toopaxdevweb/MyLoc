<?php

namespace App\Controller;

use App\Entity\Emprunt;
use App\Entity\User;
use App\Form\EmpruntType;
use App\Repository\EmpruntRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/emprunt')]
final class EmpruntController extends AbstractController
{
    #[Route(name: 'app_emprunt_index', methods: ['GET'])]
    public function index(EmpruntRepository $empruntRepository): Response
    {
        return $this->render('emprunt/index.html.twig', [
            'emprunts' => $empruntRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_emprunt_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $emprunt = new Emprunt();
        $form = $this->createForm(EmpruntType::class, $emprunt);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer l'utilisateur connecté
            /** @var User|null $user */
            $user = $this->getUser();

            if ($user === null) {
                throw $this->createAccessDeniedException('Vous devez être connecté pour faire un emprunt.');
            }

            // Récupérer l'objet de l'emprunt et sa catégorie
            $objet = $emprunt->getObjet();

            if ($objet === null) {
                throw $this->createNotFoundException('Objet non trouvé pour cet emprunt.');
            }

            $categorie = $objet->getCategorie();

            if ($categorie !== null) {
                // Récupérer les points de la catégorie
                $nombresDePointsCategorie = $categorie->getNombresDePoints();

                // Ajouter les points au nombre de points de l'utilisateur
                $user->setNombresDePoints($user->getNombresDePoints() + $nombresDePointsCategorie);

                // Persister les changements
                $entityManager->persist($user);
            }

            // Persister l'emprunt
            $entityManager->persist($emprunt);
            $entityManager->flush();

            return $this->redirectToRoute('app_emprunt_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('emprunt/new.html.twig', [
            'emprunt' => $emprunt,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_emprunt_show', methods: ['GET'])]
    public function show(Emprunt $emprunt): Response
    {
        return $this->render('emprunt/show.html.twig', [
            'emprunt' => $emprunt,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_emprunt_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Emprunt $emprunt, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EmpruntType::class, $emprunt);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_emprunt_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('emprunt/edit.html.twig', [
            'emprunt' => $emprunt,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_emprunt_delete', methods: ['POST'])]
    public function delete(Request $request, Emprunt $emprunt, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$emprunt->getId(), $request->get('_token'))) {
            $entityManager->remove($emprunt);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_emprunt_index', [], Response::HTTP_SEE_OTHER);
    }
}
