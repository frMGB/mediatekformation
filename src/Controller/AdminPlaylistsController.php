<?php

namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Repository\PlaylistRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Playlist;
use App\Form\PlaylistType;
use App\Repository\FormationRepository;

#[Route('/admin/playlists')]
class AdminPlaylistsController extends AbstractController
{
    private const TEMPLATE_PLAYLISTS = "admin/playlists/index.html.twig";

    private $playlistRepository;
    private $categorieRepository;
    private $formationRepository;
    private $em;

    public function __construct(
        PlaylistRepository $playlistRepository,
        CategorieRepository $categorieRepository,
        FormationRepository $formationRepository,
        EntityManagerInterface $em
    ) {
        $this->playlistRepository = $playlistRepository;
        $this->categorieRepository = $categorieRepository;
        $this->formationRepository = $formationRepository;
        $this->em = $em;
    }

    #[Route('', name: 'admin.playlists.index')]
    public function index(): Response
    {
        $playlists = $this->playlistRepository->findAllOrderByName('ASC');
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::TEMPLATE_PLAYLISTS, [
            'playlists' => $playlists,
            'categories' => $categories
        ]);
    }

    #[Route('/tri/{champ}/{ordre}', name: 'admin.playlists.sort')]
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
                $playlists = $this->playlistRepository->findAllOrderByName('ASC');
                break;
        }
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::TEMPLATE_PLAYLISTS, [
            'playlists' => $playlists,
            'categories' => $categories
        ]);
    }

    #[Route('/recherche/{champ}/{table}', name: 'admin.playlists.findallcontain')]
    public function findAllContain($champ, Request $request, $table = ""): Response
    {
        $valeur = $request->get("recherche");
        if ($valeur === null) {
            $valeur = "";
        }
        $playlists = $this->playlistRepository->findByContainValue($champ, $valeur, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::TEMPLATE_PLAYLISTS, [
            'playlists' => $playlists,
            'categories' => $categories,
            'valeur' => $valeur,
            'table' => $table
        ]);
    }

    #[Route('/ajout', name: 'admin.playlists.ajout')]
    public function ajout(Request $request): Response
    {
        $playlist = new Playlist();
        $form = $this->createForm(PlaylistType::class, $playlist);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($playlist);
            $this->em->flush();
            $this->addFlash('success', 'Playlist ajoutée avec succès.');
            return $this->redirectToRoute('admin.playlists.index');
        }

        return $this->render('admin/playlists/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/edit/{id}', name: 'admin.playlists.edit')]
    public function edit(Playlist $playlist, Request $request): Response
    {
        $playlistFormations = $this->formationRepository->findAllForOnePlaylist($playlist->getId());

        $form = $this->createForm(PlaylistType::class, $playlist);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'Playlist modifiée avec succès.');
            return $this->redirectToRoute('admin.playlists.index');
        }

        return $this->render('admin/playlists/edit.html.twig', [
            'playlist' => $playlist,
            'formations' => $playlistFormations, // Passer les formations au template
            'form' => $form->createView(),
        ]);
    }

    #[Route('/suppr/{id}', name: 'admin.playlists.suppr', methods: ['GET', 'POST'])]
    public function suppr(Playlist $playlist, Request $request): Response
    {
        // Vérifier si des formations sont associées
        if (!$playlist->getFormations()->isEmpty()) {
            $this->addFlash('error', 'Impossible de supprimer la playlist "' . $playlist->getName() . '" car elle contient des formations.');
            return $this->redirectToRoute('admin.playlists.index');
        }

        // Si aucune formation n'est associée, procéder à la suppression
        $this->em->remove($playlist);
        $this->em->flush();
        $this->addFlash('success', 'Playlist supprimée avec succès.');

        return $this->redirectToRoute('admin.playlists.index');
    }
}
