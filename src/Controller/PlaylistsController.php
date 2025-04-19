<?php
namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use App\Repository\PlaylistRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Contrôleur pour l'affichage des playlists côté front-office.
 */
class PlaylistsController extends AbstractController
{

    private const PLAYLISTS = "pages/playlists.html.twig";
    /**
     * @var PlaylistRepository Le repository des playlists.
     */
    private $playlistRepository;

    /**
     * @var FormationRepository Le repository des formations.
     */
    private $formationRepository;

    /**
     * @var CategorieRepository Le repository des catégories.
     */
    private $categorieRepository;

    /**
     * Constructeur de PlaylistsController.
     *
     * @param PlaylistRepository $playlistRepository Le repository des playlists.
     * @param CategorieRepository $categorieRepository Le repository des catégories.
     * @param FormationRepository $formationRespository Le repository des formations.
     */
    function __construct(
        PlaylistRepository $playlistRepository,
        CategorieRepository $categorieRepository,
        FormationRepository $formationRespository
    ) {
        $this->playlistRepository = $playlistRepository;
        $this->categorieRepository = $categorieRepository;
        $this->formationRepository = $formationRespository;
    }

    /**
     * Affiche la liste de toutes les playlists.
     *
     * @return Response La réponse HTTP contenant la page des playlists.
     */
    #[Route('/playlists', name: 'playlists')]
    public function index(): Response
    {
        $playlists = $this->playlistRepository->findAllOrderByName('ASC');
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::PLAYLISTS, [
            'playlists' => $playlists,
            'categories' => $categories
        ]);
    }

    /**
     * Trie les playlists selon un champ et un ordre donnés.
     *
     * @param string $champ Le champ sur lequel trier (`name` ou `nbformations`).
     * @param string $ordre L'ordre de tri (ASC ou DESC).
     * @return Response La réponse HTTP contenant la page avec les playlists triées.
     */
    #[Route('/playlists/tri/{champ}/{ordre}', name: 'playlists.sort')]
    public function sort($champ, $ordre): Response
    {
        switch ($champ) {
            case "name":
                $playlists = $this->playlistRepository->findAllOrderByName($ordre);
                break;
            case "nbformations":
                if ($ordre == 'ASC') {
                    $playlists = $this->playlistRepository->findAllOrderByNbFormationsASC();
                } else {
                    $playlists = $this->playlistRepository->findAllOrderByNbFormationsDESC();
                }
                break;
            default:
                $playlists = $this->playlistRepository->findAllOrderByName('DESC'); // Tri par défaut
                break;
        }
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::PLAYLISTS, [
            'playlists' => $playlists,
            'categories' => $categories
        ]);
    }

    /**
     * Recherche les playlists contenant une valeur dans un champ spécifique ou dans les catégories associées.
     *
     * @param string $champ Le champ dans lequel rechercher.
     * @param Request $request La requête HTTP contenant la valeur de recherche.
     * @param string $table Indique la table liée pour la recherche.
     * @return Response La réponse HTTP contenant la page avec les playlists filtrées.
     */
    #[Route('/playlists/recherche/{champ}/{table}', name: 'playlists.findallcontain')]
    public function findAllContain($champ, Request $request, $table = ""): Response
    {
        $valeur = $request->get("recherche");
        $playlists = $this->playlistRepository->findByContainValue($champ, $valeur, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::PLAYLISTS, [
            'playlists' => $playlists,
            'categories' => $categories,
            'valeur' => $valeur,
            'table' => $table
        ]);
    }

    /**
     * Affiche les détails d'une playlist spécifique, y compris les formations associées.
     *
     * @param int $id L'identifiant de la playlist.
     * @return Response La réponse HTTP contenant la page de détail de la playlist.
     */
    #[Route('/playlists/playlist/{id}', name: 'playlists.showone')]
    public function showOne($id): Response
    {
        $playlist = $this->playlistRepository->find($id);
        $playlistCategories = $this->categorieRepository->findAllForOnePlaylist($id);
        $playlistFormations = $this->formationRepository->findAllForOnePlaylist($id);
        return $this->render("pages/playlist.html.twig", [
            'playlist' => $playlist,
            'playlistcategories' => $playlistCategories,
            'playlistformations' => $playlistFormations
        ]);
    }
}
