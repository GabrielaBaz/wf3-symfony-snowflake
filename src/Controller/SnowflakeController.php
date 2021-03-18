<?php

namespace App\Controller;

use App\Entity\Snowflake;
use App\Form\SnowflakeType;
use App\Repository\SnowflakeRepository;
use Doctrine\Common\Annotations\Annotation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



class SnowflakeController extends AbstractController
{
    /**
     * @Route("/snowflake", name="app_snowflake")
     */
    public function home(SnowflakeRepository $snowflakeRepository): Response
    {
        $snowflakes = $snowflakeRepository->findAll();
        //dd($snowflakes);

        return $this->render('snowflake/index.html.twig', ['snowflakes' => $snowflakes]);
    }

    /**
     * @Route("/snowflake/{id}", name="app_snowflake_id",requirements={"id"="\d+"})
     */
    public function details(SnowflakeRepository $snowflakeRepository, $id): Response
    {

        //dd($request->query->get('id'));
        $snowflake = $snowflakeRepository->findOneById($id); //findOneBy(['id' => $id]);


        return $this->render('snowflake/details.html.twig', ['snowflake' => $snowflake]);
    }

    /**
     * @Route("/snowflake/snow/{id}",name="app_snowflake_snow_id")
     * THIS IS ANOTHER WAY OF DOING THE SAME THING AS THE details FUNCTION, the most optimal using symfony
     * More details in the readme file
     */
    public function details2(Snowflake $snowflake): Response
    {

        return $this->render('snowflake/details.html.twig', ['snowflake' => $snowflake]);
    }

    /**
     * @Route("/snowflake/new", name="app_snowflake_new", methods="GET|POST")
     */
    public function new(Request $request, EntityManagerInterface $manager): Response
    {
        //the var dangerous is just for an example. Then we use this variable in the create.html.twig file
        // $dangerous = "<img src='image.jpg'>";

        $snowflake = new Snowflake();
        $form = $this->createForm(SnowflakeType::class, $snowflake);

        $form->handleRequest($request);

        //The manager here inserts the new data if conditions are met in the form
        if ($form->isSubmitted() && $form->isValid()) {
            //$snowflake->setCreatedAt(new \DateTime('now'));
            //dd($request->request->get('snowflake'));

            $manager->persist($snowflake);
            $manager->flush();

            //Al final del ciclo de vida de la página y haber hecho todo lo anterior, redireccionamos
            return $this->redirect('/snowflake');
        }

        return $this->render('snowflake/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/snowflake/edit/{id<\d+>}",name="app_snowflake_edit")
     */
    public function edit(Snowflake $snowflake, Request $request, EntityManagerInterface $manager): Response
    {

        $form = $this->createForm(SnowflakeType::class, $snowflake);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->flush();

            return $this->redirectToRoute('app_snowflake');
        }

        return $this->render('snowflake/edit.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/snowflake/delete/{id<\d+>}",name="app_snowflake_delete", methods="DELETE")
     */
    public function delete(Snowflake $snowflake, Request $request, EntityManagerInterface $manager): Response
    {
        //Verifier le token

        //We generate the same token in the controler with the same code 'snowflake_deletion_' . $snowflake->getId() and
        //then we compare it to the one that comes from the form:  $request->request->get('csrf_token')
        if ($this->isCsrfTokenValid(
            'snowflake_deletion_' . $snowflake->getId(),
            $request->request->get('csrf_token')
        )) {
            $manager->remove($snowflake);
            $manager->flush();

            $this->addFlash('success', 'Your snowflake has been deleted successfully.');
        } else {

            //Si hay error pasar otro mensaje y crear una página de error en ./common/ para mostrarlo
        }
        return $this->redirectToRoute('app_snowflake');
    }
}
