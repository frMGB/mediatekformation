<?php
namespace App\Controller;

use App\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Contrôleur pour la page d'accueil et les pages statiques.
 */
class AccueilController extends AbstractController
{

    /**
     * @var FormationRepository
     */
    private $repository;

    /**
     * Constructeur de AccueilController.
     *
     * @param FormationRepository $repository Le repository des formations.
     */
    public function __construct(FormationRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Affiche la page d'accueil avec les dernières formations.
     *
     * @return Response La réponse HTTP contenant la page d'accueil.
     */
    #[Route('/', name: 'accueil')]
    public function index(): Response
    {
        $formations = $this->repository->findAllLasted(2);
        return $this->render("pages/accueil.html.twig", [
            'formations' => $formations
        ]);
    }

    /**
     * Affiche la page des Conditions Générales d'Utilisation (CGU).
     *
     * @return Response La réponse HTTP contenant la page CGU.
     */
    #[Route('/cgu', name: 'cgu')]
    public function cgu(): Response
    {
        return $this->render("pages/cgu.html.twig");
    }
}
