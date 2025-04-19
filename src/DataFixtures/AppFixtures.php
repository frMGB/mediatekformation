<?php

namespace App\DataFixtures;

use App\Entity\Categorie;
use App\Entity\Formation;
use App\Entity\Playlist;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Fixtures pour charger des données initiales dans la base de données.
 */
class AppFixtures extends Fixture
{
    /**
     * @var UserPasswordHasherInterface Service pour hasher les mots de passe.
     */
    private UserPasswordHasherInterface $passwordHasher;

    /**
     * Constructeur des fixtures.
     *
     * @param UserPasswordHasherInterface $passwordHasher Le service de hashage des mots de passe.
     */
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * Charge les données de test (catégories, playlists, utilisateur admin, formations).
     *
     * @param ObjectManager $manager L'entity manager.
     */
    public function load(ObjectManager $manager): void
    {
        // Création des Catégories
        $categoriePHP = new Categorie();
        $categoriePHP->setName('PHP');
        $manager->persist($categoriePHP);

        $categorieSymfony = new Categorie();
        $categorieSymfony->setName('Symfony');
        $manager->persist($categorieSymfony);

        $categorieTest = new Categorie();
        $categorieTest->setName('Test');
        $manager->persist($categorieTest);


        // Création des Playlists
        $playlistDevWeb = new Playlist();
        $playlistDevWeb->setName('Développement Web');
        $playlistDevWeb->setDescription('Les bases et frameworks du développement web.');
        $manager->persist($playlistDevWeb);

        $playlistBonnesPratiques = new Playlist();
        $playlistBonnesPratiques->setName('Bonnes Pratiques');
        $playlistBonnesPratiques->setDescription('Qualité, tests et bonnes habitudes.');
        $manager->persist($playlistBonnesPratiques);


        // Création de l'utilisateur admin
        $adminUser = new User();
        $adminUser->setEmail('admin@test.com');
        $adminUser->setRoles(['ROLE_ADMIN']);
        $adminUser->setPassword($this->passwordHasher->hashPassword(
            $adminUser,
            'password'
        ));
        $manager->persist($adminUser);


        // Création des Formations

        // Formation 1
        $formationSymfonyBase = new Formation();
        $formationSymfonyBase->setTitle('Introduction à Symfony 6')
            ->setDescription('Découvrez les bases du framework Symfony.')
            ->setPlaylist($playlistDevWeb)
            ->addCategory($categorieSymfony)
            ->addCategory($categoriePHP)
            ->setVideoId('abc111')
            ->setPublishedAt(new DateTime('2024-04-10'));
        $manager->persist($formationSymfonyBase);

        // Formation 2
        $formationPHPBase = new Formation();
        $formationPHPBase->setTitle('Les fondamentaux de PHP 8')
            ->setDescription('Maîtriser les bases du langage PHP.')
            ->setPlaylist($playlistDevWeb)
            ->addCategory($categoriePHP)
            ->setVideoId('def222')
            ->setPublishedAt(new DateTime('2024-01-15'));
        $manager->persist($formationPHPBase);

        // Formation 3
        $formationTestsUnitaires = new Formation();
        $formationTestsUnitaires->setTitle('Tests Unitaires en PHP avec PHPUnit')
            ->setDescription('Apprenez à écrire des tests unitaires efficaces.')
            ->setPlaylist($playlistBonnesPratiques)
            ->addCategory($categoriePHP)
            ->addCategory($categorieTest)
            ->setVideoId('ghi333')
            ->setPublishedAt(new DateTime('2024-03-01'));
        $manager->persist($formationTestsUnitaires);

        // Formation 4
        $formationSymfonyAvance = new Formation();
        $formationSymfonyAvance->setTitle('Symfony Avancé : Services et Injection')
            ->setDescription('Approfondir Symfony avec les services.')
            ->setPlaylist($playlistBonnesPratiques)
            ->addCategory($categorieSymfony)
            ->addCategory($categoriePHP)
            ->setVideoId('jkl444')
            ->setPublishedAt(new DateTime('2024-04-11'));
        $manager->persist($formationSymfonyAvance);

        // Formation 5
        $formationHTMLCSS = new Formation();
        $formationHTMLCSS->setTitle('Bases HTML5 et CSS3')
            ->setDescription('Créer la structure et le style des pages web.')
            ->setPlaylist($playlistDevWeb)
            ->setVideoId('mno555')
            ->setPublishedAt(new DateTime('2024-03-05'));
        $manager->persist($formationHTMLCSS);


        // Flush pour enregistrer tous les objets en base de données
        $manager->flush();
    }
}
