<?php
namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class CategoryController extends AbstractController{
    
    /**
     * @Route("/create", name="category_create")
     */
    public function createCategory(){
        $request = Request::createFromGlobals();

        $name = $request->request->get('name');
        $parent = $request->request->get('parent');
        $isVisible = $request->request->get('isVisible');
        $isVisible = ($isVisible === 'true') ? true : false;


        if (empty($parent)) {
            $parent = Null;
        }

        if(empty($name)){
            return $this->json(['status' => "error"]);
        }

        $slug = str_replace(" ", "-", $name);
        $slug = str_replace([".", "/", "\\"], "", $slug);
        $slug = strtolower($slug);

        $entityManager = $this->getDoctrine()->getManager();

        $category = new \App\Entity\Category();
        $category->setName($name);
        $category->setslug($slug);
        $category->setParent($parent);
        $category->setIsVisible($isVisible);

        // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $entityManager->persist($category);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();
        
        return $this->json(['status' => "sucsess"]);
    }

    /**
     * @Route("/get", name="category_get")
     */
    public function getCategory(){
        $request = Request::createFromGlobals();

        $id = $request->request->get('id');
        $slug = $request->request->get('slug');

        $category = $this->getDoctrine()
        ->getRepository(\App\Entity\Category::class);
        
        $categoryData = null;

        if (!empty($id)) {
            $categoryData = $category->find($id);
        }
        if (!empty($slug)) {
            $categoryData = $category->findBy(["slug" => strtolower($slug)]);
            $categoryData = (!empty($categoryData)) ? $categoryData[0] : Null; 
        }

        $out = [];
        $out['status'] = 'Error';
        
        if ($categoryData) {
            $out['status'] = 'Sucess';
            $out['category']['id'] = $categoryData->getId();
            $out['category']['name'] = $categoryData->getName();
            $out['category']['slug'] = $categoryData->getSlug();
            $out['category']['parent'] = $categoryData->getParent();
            $out['category']['isVisible'] = $categoryData->getIsVisible();
        }

        return $this->json($out);
    }

    /**
     * @Route("/tree", name="category_tree", methods={"POST"})
     */
    public function categoryTree(){
        $request = Request::createFromGlobals();

        $name = $request->request->get('name');

        $category = $this->getDoctrine()
        ->getRepository(\App\Entity\Category::class)
        ->find($name);

        if (!$category) {
            throw $this->createNotFoundException(
                'No categoru found for name '.$name
            );
        }
        var_dump($category);

        $out = [];
        $out['status'] = 'Sucess';
        $out['title'] = 'show category';
        $out['list'] = [];
        
        for ($i=0; $i < 10; $i++) { 
            $out['list'][$i]['id'] = $i;
            $out['list'][$i]['name'] = $i . " name";
            $out['list'][$i]['slug'] = $i . " slug";
            $out['list'][$i]['parent'] = $i . " parent";
            $out['list'][$i]['visible'] = true;
        }
   
        return $this->json($out);
    }

    /**
     * @Route("/hide", name="category_hide", methods={"PATCH"})
     */
    public function hideCategory(){

        return $this->json(['hearts' => rand(5, 100)]);
    }
}
