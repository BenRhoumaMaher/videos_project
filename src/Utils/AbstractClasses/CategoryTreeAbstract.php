<?php

namespace App\Utils\AbstractClasses;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Doctrine\DBAL\Driver\Connection;

abstract class CategoryTreeAbstract
{
    public $categoriesArrayFromdb;
    protected static $dbconnection;
    private $entityManager;
    public $urlgenerator;

    public function __construct(EntityManagerInterface $entityManager, UrlGeneratorInterface $urlgenerator)
    {
        $this->entityManager = $entityManager;
        $this->urlgenerator = $urlgenerator;
        $this->categoriesArrayFromdb = $this->getCategories();
    }

    abstract public function getCategoryList(array $categories_array);

    public function buildTree(int $parent_id = null): array
    {
        $subcategory = [];
        foreach($this->categoriesArrayFromdb as $category)
        {
            if($category['parent_id'] == $parent_id)
            {
                $children = $this->buildTree($category['id']);
                if($children)
                {
                    $category['children'] = $children;
                }
                $subcategory[] = $category;
            }
        }
        return $subcategory;
    }

    private function getCategories(): array
    {
        if (self::$dbconnection) {
            return self::$dbconnection;
        } else {
            $sql = "SELECT * FROM categories";
            $result = $this->entityManager->getConnection()->executeQuery($sql)->fetchAllAssociative();

            return self::$dbconnection = $result;
        }
    }
}
