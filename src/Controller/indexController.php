<?php
namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class indexController extends AbstractController{
    
    /**
     * @Route("/", name="category_show")
     */
    public function show(){

        $out = [];
        $out['status'] = 'Sucess';
        $out['title'] = 'show category';
        $out['name'] = 'a';

        return $this->render('category/show.html.twig', $out);
    }
}