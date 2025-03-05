<?php


namespace App\Controller;

use App\Entity\Cessation;
use App\Entity\Enum\EtatCompte;
use App\Repository\CessationRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;



#[\Symfony\Component\Routing\Attribute\Route('/cessation/')]

class cessationController extends AbstractController
{
    private $cessationRepository;
    private $userRepository;
    private $entityManager;

    public function __construct(CessationRepository $cessationRepository, UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        $this->cessationRepository = $cessationRepository;
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }




    #[Route('createCessation/{userId}', name: 'create_cessation', methods: ['POST'])]
    public function createCessation(int $userId, Request $request, MailerInterface $mailer): Response
    {
        $user = $this->userRepository->find($userId);

        if (!$user) {
            return $this->json(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $existingCessation = $this->entityManager->getRepository(Cessation::class)->findOneBy(['idUser' => $userId]);

        if ($existingCessation) {
            return $this->json(['error' => 'Cessation already exists for this user'], Response::HTTP_BAD_REQUEST);
        }

        $motif = $request->request->get('motif');

        if (!$motif) {
            return $this->json(['error' => 'Motif is required'], Response::HTTP_BAD_REQUEST);
        }

        $cessation = new Cessation();
        $cessation->setMotif($motif);
        $cessation->setDateMotif(new DateTime());
        $cessation->setIdUser($user);

        $user->setEtatCompte(EtatCompte::Inactive);

        $this->entityManager->persist($cessation);
        $this->entityManager->persist($user);

        $this->entityManager->flush();

        $projectDir = $this->getParameter('kernel.project_dir');
        $imagePath = $projectDir . '/public/images/educo.jpg';

        $email = (new Email())
            ->from('noreply@votreapp.com')
            ->to($user->getEmail())
            ->subject('Votre compte a été désactivé')
            ->html(
                '<html>
        <head>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    background-color: #f8f9fa;
                    padding: 20px;
                }
                .container {
                    background-color: #ffffff;
                    border-radius: 8px;
                    padding: 30px;
                    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                }
                .btn {
                    display: inline-block;
                    padding: 10px 20px;
                    background-color: #007bff;
                    color: white;
                    text-decoration: none;
                    border-radius: 5px;
                    font-weight: bold;
                }
                .btn:hover {
                    background-color: #0056b3;
                }
                h1 {
                    color: #343a40;
                }
                p {
                    color: #6c757d;
                    font-size: 16px;
                }
                .logo {
                    width: 100%;
                    max-width: 300px;
                    display: block;
                    margin: 20px auto;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <h1>Votre compte a été désactivé</h1>
                <p>Bonjour ' . $user->getFullName() . ',</p>
                <p>Nous vous informons que votre compte a été désactivé pour le motif suivant :</p>
                <p><strong>' . $motif . '</strong></p>
                <p>En conséquence, vous ne pourrez plus vous connecter à votre compte.</p>
                <p>Si vous avez des questions, n\'hésitez pas à contacter notre support.</p>
                <p>Cordialement,</p>
                <p><strong>L\'équipe de support</strong></p>
                <a href="https://127.0.0.1:8000/login" class="btn">Visiter notre site</a>
                <img class="logo" src="cid:educo_logo" alt="Logo de notre site" />
            </div>
        </body>
    </html>'
            )
            ->attachFromPath($imagePath, 'educo.jpg', 'image/jpeg')
            ->embedFromPath($imagePath, 'educo_logo');

        $mailer->send($email);

        return $this->redirectToRoute('get_all_cessations');
    }







    #[Route('/activateAccount/{id}', name: 'activate_account', methods: ['POST'])]
    public function activateAccount(int $id): Response
    {
        $cessation = $this->cessationRepository->find($id);

        if (!$cessation) {
            return $this->json(['error' => 'Cessation not found'], Response::HTTP_NOT_FOUND);
        }

        $user = $cessation->getIdUser();

        if ($user) {
            $user->setEtatCompte(EtatCompte::Active);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }

        $cessation->setIdUser(null);
        $this->entityManager->persist($cessation);
        $this->entityManager->flush();

        $this->entityManager->remove($cessation);
        $this->entityManager->flush();

        return $this->redirectToRoute('get_all_cessations');
    }


    #[Route('getAllCessation', name: 'get_all_cessations', methods: ['GET'])]
    public function getAllCessations(): Response
    {
        $cessations = $this->cessationRepository->findAll();

        return $this->render('cessation/all_cessations.html.twig', [
            'cessations' => $cessations
        ]);
    }

    #[Route('/', name: 'app_chercherCessation_list')]
    public function chercherCessation(Request $request, CessationRepository $cessationRepository): Response
    {
        $search = $request->query->get('search');

        if ($search && !preg_match('/^(?! )[ \p{L}-]+(?<! )$/u', $search)) {
            return $this->json(['error' => 'Recherche invalide'], 400);
        }

        $queryBuilder = $cessationRepository->createQueryBuilder('c')
            ->leftJoin('c.idUser', 'u')
            ->addSelect('u');

        if ($search) {
            $queryBuilder->where('u.nom LIKE :search OR u.prenom LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        $cessations = $queryBuilder->getQuery()->getResult();

        if ($request->isXmlHttpRequest()) {
            return $this->json([
                'html' => $this->renderView('cessation/_cessation_list.html.twig', ['cessations' => $cessations])
            ]);
        }

        return $this->render('cessation/all_cessations.html.twig', ['cessations' => $cessations]);
    }



}
