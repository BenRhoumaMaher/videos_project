<?php
namespace App\Utils;

use App\Twig\Extension\AppExtension;
use App\Utils\AbstractClasses\CategoryTreeAbstract;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CategoryTreeFrontPage extends CategoryTreeAbstract {


    public $html_1 = '<ul>';
    public $html_2 = '<li>';
    public $html_3 = '<a href="';
    public $html_4 = '">';
    public $html_5 = '</a>';
    public $html_6 = '</li>';
    public $html_7 = '</ul>';

    public function getCategoryListAndParent(int $id): string
    {
        $this->slugger = new AppExtension; // Twig extension to slugify url's for categories
        $parentData = $this->getMainParent($id); // main parent of subcategory
        $this->mainParentName = $parentData['name']; // for accesing in view
        $this->mainParentId = $parentData['id']; // for accesing in view
        $key = array_search($id, array_column($this->categoriesArrayFromdb,'id'));
        $this->currentCategoryName = $this->categoriesArrayFromdb[$key]['name']; // for accesing in view
        $categories_array = $this->buildTree($parentData['id']); // builds array for generating nested html list
        return $this->getCategoryList($categories_array);
    }

    public $categorylist = '';
    public function getCategoryList(array $categories_array)
    {
        $this->categorylist .= $this->html_1;
        foreach ($categories_array as $value)
        {   
            $catName = $this->slugger->slugify($value['name']);
            
            $url = $this->urlgenerator->generate('video_list', ['categoryname'=>$catName, 'id'=>$value['id']]);
            $this->categorylist .= $this->html_2 . $this->html_3 . $url . $this->html_4 . $catName . $this->html_5;
            if(!empty($value['children']))
            {
                $this->getCategoryList($value['children']);
            }
            $this->categorylist .= $this->html_6;
            
        }
        $this->categorylist .= $this->html_7;
        return $this->categorylist;
    }

    public function getMainParent(int $id): array
    {
        $key = array_search($id, array_column($this->categoriesArrayFromdb, 'id'));
        if($this->categoriesArrayFromdb[$key]['parent_id'] != null)
        {
            return $this->getMainParent($this->categoriesArrayFromdb[$key]['parent_id']);
        }
        else
        {
            return [
                'id'=>$this->categoriesArrayFromdb[$key]['id'],
                'name'=>$this->categoriesArrayFromdb[$key]['name']
                ];
        }
    }
 
}
