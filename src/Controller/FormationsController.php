<?php
namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Contrôleur pour l'affichage des formations côté front-office.
 */
class FormationsController extends AbstractController
{

    private const FORMATIONS = "pages/formations.html.twig";

    /**
     * @var FormationRepository Le repository des formations.
     */
    private $formationRepository;

    /**
     * @var CategorieRepository Le repository des catégories.
     */
    private $categorieRepository;

    /**
     * Constructeur de FormationsController.
     *
     * @param FormationRepository $formationRepository Le repository des formations.
     * @param CategorieRepository $categorieRepository Le repository des catégories.
     */
    function __construct(FormationRepository $formationRepository, CategorieRepository $categorieRepository)
    {
        $this->formationRepository = $formationRepository;
        $this->categorieRepository = $categorieRepository;
    }

    /**
     * Affiche la liste de toutes les formations.
     *
     * @return Response La réponse HTTP contenant la page des formations.
     */
    #[Route('/formations', name: 'formations')]
    public function index(): Response
    {
        $formations = $this->formationRepository->findAll();
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::FORMATIONS, [
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
    #[Route('/formations/tri/{champ}/{ordre}/{table}', name: 'formations.sort')]
    public function sort($champ, $ordre, $table = ""): Response
    {
        $formations = $this->formationRepository->findAllOrderBy($champ, $ordre, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::FORMATIONS, [
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
    #[Route('/formations/recherche/{champ}/{table}', name: 'formations.findallcontain')]
    public function findAllContain($champ, Request $request, $table = ""): Response
    {
        $valeur = $request->get("recherche");
        $formations = $this->formationRepository->findByContainValue($champ, $valeur, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::FORMATIONS, [
            'formations' => $formations,
            'categories' => $categories,
            'valeur' => $valeur,
            'table' => $table
        ]);
    }

    /**
     * Affiche les détails d'une formation spécifique.
     *
     * @param int $id L'identifiant de la formation.
     * @return Response La réponse HTTP contenant la page de détail de la formation.
     */
    #[Route('/formations/formation/{id}', name: 'formations.showone')]
    public function showOne($id): Response
    {
        $formation = $this->formationRepository->find($id);
        return $this->render("pages/formation.html.twig", [
            'formation' => $formation
        ]);
    }
}
