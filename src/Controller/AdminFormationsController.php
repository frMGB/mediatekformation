<?php
namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Formation;
use App\Form\FormationType;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Contrôleur pour la gestion des formations dans l'administration.
 */
#[Route('/admin/formations')]
class AdminFormationsController extends AbstractController
{
    private const TEMPLATE_FORMATIONS = "admin/formations/index.html.twig";

    /**
     * @var FormationRepository Le repository des formations.
     */
    private $formationRepository;
    /**
     * @var CategorieRepository Le repository des catégories.
     */
    private $categorieRepository;

    /**
     * Constructeur de AdminFormationsController.
     *
     * @param FormationRepository $formationRepository Le repository des formations.
     * @param CategorieRepository $categorieRepository Le repository des catégories.
     */
    public function __construct(FormationRepository $formationRepository, CategorieRepository $categorieRepository)
    {
        $this->formationRepository = $formationRepository;
        $this->categorieRepository = $categorieRepository;
    }

    /**
     * Affiche la liste des formations et des catégories.
     *
     * @return Response La réponse HTTP contenant la page d'administration des formations.
     */
    #[Route('', name: 'admin.formations.index')]
    public function index(): Response
    {
        $formations = $this->formationRepository->findAll();
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::TEMPLATE_FORMATIONS, [
            'formations' => $formations,
            'categories' => $categories
        ]);
    }

    /**
     * Trie les formations selon un champ et un ordre donnés.
     *
     * @param string $champ Le champ sur lequel trier.
     * @param string $ordre L'ordre de tri (ASC ou DESC).
     * @param string $table La table liée pour le tri.
     * @return Response La réponse HTTP contenant la page avec les formations triées.
     */
    #[Route('/tri/{champ}/{ordre}/{table}', name: 'admin.formations.sort')]
    public function sort($champ, $ordre, $table = ""): Response
    {
        $formations = $this->formationRepository->findAllOrderBy($champ, $ordre, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::TEMPLATE_FORMATIONS, [
            'formations' => $formations,
            'categories' => $categories
        ]);
    }

    /**
     * Recherche les formations contenant une valeur dans un champ spécifique.
     *
     * @param string $champ Le champ dans lequel rechercher.
     * @param Request $request La requête HTTP contenant la valeur de recherche.
     * @param string $table La table liée pour la recherche.
     * @return Response La réponse HTTP contenant la page avec les formations filtrées.
     */
    #[Route('/recherche/{champ}/{table}', name: 'admin.formations.findallcontain')]
    public function findAllContain($champ, Request $request, $table = ""): Response
    {
        $valeur = $request->get("recherche");
        if ($valeur === null) {
            $valeur = "";
        }
        $formations = $this->formationRepository->findByContainValue($champ, $valeur, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::TEMPLATE_FORMATIONS, [
            'formations' => $formations,
            'categories' => $categories,
            'valeur' => $valeur,
            'table' => $table
        ]);
    }

    /**
     * Gère l'ajout d'une nouvelle formation.
     *
     * @param Request $request La requête HTTP.
     * @param EntityManagerInterface $em L'entity manager.
     * @return Response La réponse HTTP (redirection ou affichage du formulaire).
     */
    #[Route('/ajout', name: 'admin.formations.ajout')]
    public function ajout(Request $request, EntityManagerInterface $em): Response
    {
        $formation = new Formation();
        $form = $this->createForm(FormationType::class, $formation);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($formation);
            $em->flush();
            $this->addFlash('success', 'Formation ajoutée avec succès.');
            return $this->redirectToRoute('admin.formations.index');
        }

        return $this->render('admin/formations/add.html.twig', [
            'formation' => $formation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Gère la modification d'une formation existante.
     *
     * @param Formation $formation La formation à modifier.
     * @param Request $request La requête HTTP.
     * @param EntityManagerInterface $em L'entity manager.
     * @return Response La réponse HTTP (redirection ou affichage du formulaire).
     */
    #[Route('/edit/{id}', name: 'admin.formations.edit')]
    public function edit(Formation $formation, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(FormationType::class, $formation);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Formation modifiée avec succès.');
            return $this->redirectToRoute('admin.formations.index');
        }

        return $this->render('admin/formations/edit.html.twig', [
            'formation' => $formation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Gère la suppression d'une formation.
     *
     * @param Formation $formation La formation à supprimer.
     * @param EntityManagerInterface $em L'entity manager.
     * @return Response La réponse HTTP (redirection vers l'index des formations).
     */
    #[Route('/suppr/{id}', name: 'admin.formations.suppr', methods: ['GET', 'POST'])]
    public function suppr(Formation $formation, EntityManagerInterface $em): Response
    {
        $em->remove($formation);
        $em->flush();
        $this->addFlash('success', 'Formation supprimée avec succès.');

        return $this->redirectToRoute('admin.formations.index');
    }
}
