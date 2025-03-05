<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

class StatistiqueService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getUsersByRole(): array
    {
        $query = $this->entityManager->createQuery("
        SELECT u.roles, COUNT(u.id) as count
        FROM App\Entity\User u
    ");

        $result = $query->getResult();
        $roleCounts = [];

        foreach ($result as $row) {
            $roles = is_array($row['roles']) ? $row['roles'] : json_decode($row['roles'], true);

            if (!is_array($roles)) {
                continue;
            }

            foreach ($roles as $role) {
                if (!isset($roleCounts[$role])) {
                    $roleCounts[$role] = 0;
                }
                $roleCounts[$role] += $row['count'];
            }
        }

        $formattedData = [];
        foreach ($roleCounts as $role => $count) {
            $formattedData[] = [
                'role' => $this->formatRoleName($role),
                'count' => $count
            ];
        }

        return $formattedData;
    }

  function formatRoleName(string $role): string
    {
        return match ($role) {
            'Administrateur' => 'Administrateur',
            'Parent' => 'Parent',
            'Enseignant' => 'Enseignant',
            default => ucfirst(strtolower(str_replace('_', ' ', str_replace('ROLE_', '', $role))))
        };
    }





    public function getUsersByGender(): array
    {
        $query = $this->entityManager->createQuery("
                SELECT u.genre, COUNT(u.id) as count
                FROM App\Entity\User u
                GROUP BY u.genre
            ");

        return $query->getResult();
    }



    public function getReclamationsByMonth(): array
    {
        $conn = $this->entityManager->getConnection();

        $sql = "
            SELECT DATE_FORMAT(date_de_creation, '%Y-%m') AS month, COUNT(id) AS count
            FROM reclamation
            WHERE date_de_creation IS NOT NULL
            GROUP BY month
            ORDER BY month ASC
        ";

        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();

        return $resultSet->fetchAllAssociative();
    }



}