<?php

namespace App\Controller;

use App\Entity\Products;
use App\Form\ProductsType;
use App\Repository\ProductsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/products")
 */
class ProductsController extends AbstractController
{
    /**
     * @Route("/", name="app_products_index", methods={"GET"})
     */
    public function index(ProductsRepository $productsRepository): Response
    {
        return $this->json([
            'products' => $productsRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_products_new", methods={"GET", "POST"})
     */
    public function new(Request $request, ProductsRepository $productsRepository): Response
    {
        $product = new Products();
        $data = json_decode($request->getContent(), true);
        $product
            ->setDescription($data['description'])
            ->setPrice($data['price'])
            ->setStatus($data['status'])
            ->setCreatedAt(new \DateTime("now", new \DateTimeZone("America/Sao_Paulo")))
            ->setUpdatedAt(new \DateTime("now", new \DateTimeZone("America/Sao_Paulo")))
        ;

        $doctrine = $this->getDoctrine()->getManager();
        $doctrine->persist($product);
        $doctrine->flush();

        return $this->json([
            'ProductCreated' => $product
        ]);
    }

    /**
     * @Route("/findOne/{id}", name="app_products_show", methods={"GET"})
     */
    public function show(Products $product): Response
    {
        return $this->render('products/show.html.twig', [
            'product' => $product,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_products_edit", methods={"PUT"})
     */
    public function edit(Request $request, $id): Response
    {

        $data = json_decode($request->getContent(), true);
        $entityManager = $this->getDoctrine()->getManager();
        $product = $entityManager->getRepository(Products::class)->find($id);

        $product
            ->setDescription($data['description'])
            ->setPrice($data['price'])
            ->setStatus($data['status'])
            ->setUpdatedAt(new \DateTime("now", new \DateTimeZone("America/Sao_Paulo")))
        ;

        $doctrine = $this->getDoctrine()->getManager();
        $doctrine->flush();

        return $this->json([
            'ProductUpdated' => $product
        ]);
    }

    /**
     * @Route("/delete/{id}", name="app_products_delete", methods={"DELETE"})
     */
    public function delete($id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $produtc = $entityManager->getRepository(Products::class)->find($id);
 
        if (!$produtc) {
            return $this->json( [
                "Error" => 'No produtc found for id' ." ".$id,
            ]);
        }
 
        $entityManager->remove($produtc);
        $entityManager->flush();
 
        return $this->json([
                "SuccessDeleted" => $produtc
        ]);
    }


    /**
     * @Route("/active", name="app_produtc_active", methods={"GET"})
     */
    public function active(): Response
    {   

        $entityManager = $this->getDoctrine()->getManager();
        $produtcActive =  $entityManager->getRepository(Products::class)
                            ->findAllActive(true);
   
        return $this->json([
            'produtc' => $produtcActive,
        ]);
    }

    /**
     * @Route("/disabled", name="app_produtc_disabled", methods={"GET"})
     */
    public function disabled(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $produtcDisabled =  $entityManager->getRepository(Products::class)
                                    ->findAllActive(false);
        return $this->json([
            'produtc' => $produtcDisabled,
        ]);
    }

    /**
     * @Route("/disable/{id}", name="app_produtc_disable", methods={"PUT"})
     */
    public function disableProdutc($id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $produtc = $entityManager->getRepository(Products::class)->find($id);

        if (!$produtc) {
            return $this->json( [
                "Error" => 'No produtc found for id' ." ".$id,
            ]);
        }
        
        $produtc
            ->setStatus(false)
            ->setUpdatedAt(new \DateTime("now", new \DateTimeZone("America/Sao_Paulo")))
        ;

        $doctrine = $this->getDoctrine()->getManager();
        $doctrine->flush();

        return $this->json([
            'Success' => 'produtc '.$id. ' disabled',
        ]);
    }
}
