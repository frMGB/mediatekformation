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

#[Route('/admin/formations')]
class AdminFormationsController extends AbstractController
{
    private const TEMPLATE_FORMATIONS = "admin/formations/index.html.twig";

    private $formationRepository;
    private $categorieRepository;

    public function __construct(FormationRepository $formationRepository, CategorieRepository $categorieRepository)
    {
        $this->formationRepository = $formationRepository;
        $this->categorieRepository = $categorieRepository;
    }

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

    #[Route('/suppr/{id}', name: 'admin.formations.suppr', methods: ['GET', 'POST'])]
    public function suppr(Formation $formation, Request $request, EntityManagerInterface $em): Response
    {
        $em->remove($formation);
        $em->flush();
        $this->addFlash('success', 'Formation supprimée avec succès.');

        return $this->redirectToRoute('admin.formations.index');
    }
}
