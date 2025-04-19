<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Contrôleur pour la gestion des catégories dans l'administration.
 */
#[Route('/admin/categories')]
class AdminCategoriesController extends AbstractController
{
    private const TEMPLATE_CATEGORIES = 'admin/categories/index.html.twig';

    /**
     * @var CategorieRepository Le repository des catégories.
     */
    private $categorieRepository;
    /**
     * @var EntityManagerInterface L'entity manager.
     */
    private $em;

    /**
     * Constructeur de AdminCategoriesController.
     *
     * @param CategorieRepository $categorieRepository Le repository des catégories.
     * @param EntityManagerInterface $em L'entity manager.
     */
    public function __construct(CategorieRepository $categorieRepository, EntityManagerInterface $em)
    {
        $this->categorieRepository = $categorieRepository;
        $this->em = $em;
    }

    /**
     * Affiche la liste des catégories et le formulaire d'ajout.
     *
     * @return Response La réponse HTTP contenant la page d'administration des catégories.
     */
    #[Route('', name: 'admin.categories.index', methods: ['GET'])]
    public function index(): Response
    {
        $categories = $this->categorieRepository->findBy([], ['name' => 'ASC']);

        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie, [
            'action' => $this->generateUrl('admin.categories.ajout'),
            'method' => 'POST',
        ]);

        return $this->render(self::TEMPLATE_CATEGORIES, [
            'categories' => $categories,
            'form' => $form->createView()
        ]);
    }

    /**
     * Gère l'ajout d'une nouvelle catégorie.
     *
     * @param Request $request La requête HTTP.
     * @return Response La réponse HTTP (redirection ou affichage du formulaire avec erreurs).
     */
    #[Route('/ajout', name: 'admin.categories.ajout', methods: ['GET', 'POST'])]
    public function ajout(Request $request): Response
    {
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérification manuelle de l'unicité
            $existingCategorie = $this->categorieRepository->findOneBy(['name' => $categorie->getName()]);
            if ($existingCategorie) {
                $this->addFlash('error', 'Le nom de catégorie "' . $categorie->getName() . '" existe déjà.');
            } else {
                $this->em->persist($categorie);
                $this->em->flush();
                $this->addFlash('success', 'Catégorie "' . $categorie->getName() . '" ajoutée avec succès.');
                return $this->redirectToRoute('admin.categories.index');
            }
        } elseif ($form->isSubmitted()) {
            $this->addFlash('error', 'Erreur dans le formulaire. Veuillez corriger les erreurs.');
        }

        $categories = $this->categorieRepository->findBy([], ['name' => 'ASC']);

        return $this->render(self::TEMPLATE_CATEGORIES, [
            'categories' => $categories,
            'form' => $form->createView()
        ]);
    }

    /**
     * Gère la suppression d'une catégorie.
     *
     * @param Categorie $categorie La catégorie à supprimer.
     * @return Response La réponse HTTP (redirection vers l'index des catégories).
     */
    #[Route('/suppr/{id}', name: 'admin.categories.suppr', methods: ['GET', 'POST'])]
    public function suppr(Categorie $categorie): Response
    {
        if (!$categorie->getFormations()->isEmpty()) {
            $this->addFlash('error', 'Impossible de supprimer la catégorie "' . $categorie->getName() . '" car elle est utilisée par des formations.');
        } else {
            $this->em->remove($categorie);
            $this->em->flush();
            $this->addFlash('success', 'Catégorie "' . $categorie->getName() . '" supprimée avec succès.');
        }

        return $this->redirectToRoute('admin.categories.index');
    }
}
