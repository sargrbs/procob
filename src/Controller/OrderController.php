<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\User;
use App\Entity\Products;
use App\Form\OrderType;
use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
/**
 * @Route("/order")
 */
class OrderController extends AbstractController
{
    /**
     * @Route("/", name="app_order_index", methods={"GET"})
     */
    public function index(OrderRepository $orderRepository): Response
    {
        return $this->json([
            'orders' => $orderRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_order_new", methods={"GET", "POST"})
     */
    public function new(Request $request, OrderRepository $orderRepository): Response
    {
        $order = new Order();
        $data = json_decode($request->getContent(), true);
        $entityManager = $this->getDoctrine()->getManager();

        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $userId = $propertyAccessor->getValue($user, 'id');
        
        $item = $entityManager->getReference(Products::class, $data['products']);
        $userOrder = $entityManager->getReference(User::class, $userId);

        $order
            ->setOrderNumber($data['orderNumber'])
            ->addProduct($item)
            ->setStatus($data['status'])
            ->setPaidPurchase($data['PaidPurchase'])
            ->setUser($userOrder)
            ->setCreatedAt(new \DateTime("now", new \DateTimeZone("America/Sao_Paulo")))
            ->setUpdatedAt(new \DateTime("now", new \DateTimeZone("America/Sao_Paulo")))
        ;

        $doctrine = $this->getDoctrine()->getManager();
        $doctrine->persist($order);
        $doctrine->flush();

        $entityManager = $this->getDoctrine()->getManager();
        $orderId = $propertyAccessor->getValue($order, 'id');
        $orderCreated = $entityManager->getRepository(Order::class)->findOne($orderId);

        return $this->json([
            'OrdertCreated' => $orderCreated
        ]);
    }

    /**
     * @Route("/findOne/{id}", name="app_order_show", methods={"GET"})
     */
    public function show($id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $order = $entityManager->getRepository(Order::class)->findOne($id);

        return $this->json([
            'order' => $order,
        ]);
    }

    /**
     * @Route("/edit/{id}", name="app_order_edit", methods={"PUT"})
     */
    public function edit(Request $request, $id): Response
    {
        $data = json_decode($request->getContent(), true);
        $entityManager = $this->getDoctrine()->getManager();
        $order = $entityManager->getRepository(Order::class)->find($id);
        
        $order
            ->setStatus($data['status'])
            ->setUpdatedAt(new \DateTime("now", new \DateTimeZone("America/Sao_Paulo")))
        ;
        if(count($data) > 1){
            $order
                ->setStatus($data['status'])
                //if paid purchase must be updated
                ->setPaidPurchase($data['paid_purchase']) 
                ->setUpdatedAt(new \DateTime("now", new \DateTimeZone("America/Sao_Paulo")))
            ;
        }
        

        $doctrine = $this->getDoctrine()->getManager();
        $doctrine->flush();

        $entityManager = $this->getDoctrine()->getManager();
        $orderUpdated = $entityManager->getRepository(Order::class)->findOne($id);

        return $this->json([
            'OrderUpdated' => $orderUpdated,
        ]);
    }

    /**
     * @Route("/delete/{id}", name="_deleteOrder", methods={"DELETE"})
     * 
     */
    public function delete($id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $order = $entityManager->getRepository(Order::class)->find($id);
 
        if (!$order) {
            return $this->json( [
                "Error" => 'No user found for id' ." ".$id,
            ]);
        }
 
        $entityManager->remove($order);
        $entityManager->flush();
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $orderNumber = $propertyAccessor->getValue($order, 'orderNumber');

        return $this->json([
                "SuccessDeleteOrderNumber" => $orderNumber
        ]);
 
    }

    /**
     * @Route("/active", name="app_order_active", methods={"GET"})
     */
    public function active(): Response
    {   

        $entityManager = $this->getDoctrine()->getManager();
        $OrderActive =  $entityManager->getRepository(Order::class)
                            ->findAllActive(true);
   
        return $this->json([
            'ActiveOrders' => $OrderActive,
        ]);
    }

    /**
     * @Route("/disabled", name="app_order_disabled", methods={"GET"})
     */
    public function disabled(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $OrderDisabled =  $entityManager->getRepository(Order::class)
                            ->findAllActive(false);
   
        return $this->json([
            'DisabledOrders' => $OrderDisabled,
        ]);
    }

    /**
     * @Route("/disable/{id}", name="app_order_disable", methods={"PUT"})
     */
    public function disableOrder($id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $order = $entityManager->getRepository(Order::class)->find($id);

        $order
            ->setStatus(false)
            ->setUpdatedAt(new \DateTime("now", new \DateTimeZone("America/Sao_Paulo")))
        ;

        $doctrine = $this->getDoctrine()->getManager();
        $doctrine->flush();

        return $this->json([
            'Success' => 'Order '.$id. ' disabled',
        ]);
    }

    /**
     * @Route("/paid", name="app_order_paid", methods={"GET"})
     */
    public function paid(): Response
    {   

        $entityManager = $this->getDoctrine()->getManager();
        $Orderpaid =  $entityManager->getRepository(Order::class)
                            ->findAllpaid(true);
   
        return $this->json([
            'paidOrders' => $Orderpaid,
        ]);
    }

    /**
     * @Route("/unpaid", name="app_order_unpaid", methods={"GET"})
     */
    public function unpaid(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $Orderunpaid =  $entityManager->getRepository(Order::class)
                            ->findAllpaid(false);
   
        return $this->json([
            'unpaidOrders' => $Orderunpaid,
        ]);
    }
}
