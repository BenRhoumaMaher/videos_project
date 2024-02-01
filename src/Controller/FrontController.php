<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\VideoRepository;
use App\Utils\CategoryTreeFrontPage;
use phpDocumentor\Reflection\Types\Void_;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class FrontController extends AbstractController
{

    private $categoryRepository;
    private $videoRepository;
    public function __construct(CategoryRepository $categoryRepository, VideoRepository $videoRepository)
    {
        $this->categoryRepository = $categoryRepository;
        $this->videoRepository = $videoRepository;
    }
    #[Route('/front', name: 'app_front')]
    public function index(): Response
    {
        return $this->render('front/index.html.twig');
    }

    #[Route('/video-list/category/{categoryname},{id}', name: 'video_list')]
    public function videoList($id, CategoryTreeFrontPage $categories, Request $request): Response
    {
        $categories->getCategoryListAndParent($id);
        $ids = $categories->getChildIds($id);
        array_push($ids,$id);
        $videos = $this->videoRepository->findByChildIds($ids,
            $request->query->getInt('page',1),10,$request->get('sortBy')
        );
        return $this->render('front/video_list.html.twig',[
            'subcategories' => $categories,
            'videos' => $videos
        ]);
    }


    #[Route('/video-details', name: 'video_details')]
    public function videoDetails(): Response
    {
        return $this->render('front/video_details.html.twig');
    }

    #[Route('/search-results', methods: ['GET'], name: 'search_results', defaults: ['page' => '1'])]
    public function searchResults($page, Request $request)
    {
        $videos = null;
        $query = null;

        if($query = $request->get('query'))
        {
            $videos = $this->videoRepository->findByTitle($query, $page, $request->get('sortBy'));

            if(!$videos->getItems()) $videos = null;
        }
       
        return $this->render('front/search_results.html.twig',[
            'videos' => $videos,
            'query' => $query,
        ]);
    }

    #[Route('/pricing', methods: 'GET', name: 'pricing')]
    public function pricing(): Response
    {
        return $this->render('front/pricing.html.twig');
    }

    #[Route('/register', name: 'register')]
    public function register(): Response
    {
        return $this->render('front/register.html.twig');
    }

    #[Route('/login', name: 'login')]
    public function login(AuthenticationUtils $helper): Response
    {
        return $this->render('front/login.html.twig',[
            'error' => $helper->getLastAuthenticationError()
        ]);
    }

    #[Route('/logout', name: 'logout')]

    public function logout(): Void
    {
        throw new \Exception('this should never be reached!');
    }

    #[Route('/payment', name: 'payment')]
    public function payment(): Response
    {
        return $this->render('front/payment.html.twig');
    }

    public function mainCategories()
    {
        $categories = $this->categoryRepository->findBy(['parent' => null], ['name' => 'ASC']);
        return $this->render('front/_main_categories.html.twig', [
            'categories' => $categories
        ]);
    }


}
