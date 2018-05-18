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
            return $this->json(['status' => "Error"]);
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
        
        return $this->json(['status' => "Success"]);
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
            $categoryData = $category->findOneBy(["slug" => strtolower($slug)]);
            // $categoryData = (!empty($categoryData)) ? $categoryData[0] : Null;
        }

        $out = [];
        $out['status'] = 'Nothing Found';
        
        if ($categoryData) {
            $out['status'] = 'Success';
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

        $id = $request->request->get('id');

        $categories = $this->getDoctrine()
        ->getRepository(\App\Entity\Category::class)
        ->findBy(["parent" => $id]);

        $out = [];
        $out['status'] = 'Nothing Found';
        $out['children'] = [];
        
        foreach ($categories as $categoryData) {
            $category = array(
                'id' => $categoryData->getId(),
                'name'=> $categoryData->getName(),
                'slug' => $categoryData->getSlug(),
                'parent' => $categoryData->getParent(),
                'isVisible' => $categoryData->getIsVisible()
            );

            $out['children'][] = $category;
        }
        if (!empty($out['children'])) {
            $out['status'] = 'Success';
        }
        return $this->json($out);
    }

    /**
     * @Route("/hide", name="category_hide")
     */
    public function hideCategory(){
        $request = Request::createFromGlobals();

        $id = $request->request->get('id');

        $category = $this->getDoctrine()->getManager()->getRepository(\App\Entity\Category::class)->find($id); 
        $category->setIsVisible(!$category->getIsVisible()); 
        
        $this->getDoctrine()->getManager()->persist($category);
        $this->getDoctrine()->getManager()->flush();

        $out = [];
        $out['status'] = 'Success';
        $out['category']['id'] = $category->getId();
        $out['category']['name'] = $category->getName();
        $out['category']['slug'] = $category->getSlug();
        $out['category']['parent'] = $category->getParent();
        $out['category']['isVisible'] = $category->getIsVisible();

        return $this->json($out);
    }
}
