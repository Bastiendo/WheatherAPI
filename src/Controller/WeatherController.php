<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\WeatherService;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;

class WeatherController extends AbstractController
{
    private $weatherService;

    public function __construct(WeatherService $weather)
    {
        $this->weatherService = $weather;
    }

    /**
     * @Route("/weather", name="weather")
     */
    public function index(Request $request)
    {
        #return new Response(json_encode($this->weatherService->getWeather()))

        $default = ['message' => 'Type your message here'];
        $form = $this->createFormBuilder($default)
            ->add('ville', TextType::class, array(
                'attr' => array(
                'placeholder' => 'ta ville',
                )))
            ->add('lon', NumberType::class, array(
                "required" => false,
                'label' => false,
                'attr' => array(
                    'class' => 'd-none',
                    )
            ))
            ->add('lat', NumberType::class, array(
                "required" => false,
                'label' => false,
                'attr' => array(
                    'class' => 'd-none',
                    )
            ))
            ->add('position', CheckboxType::class, array(
                'required' => false,
                'label' => 'utilisez votre position '
            ))
            ->add('envoyer',SubmitType::class)
            ->getForm();

        $data =  $this->weatherService->getWeather();
        $dataPrevisionnel =  $this->weatherService->getPrevisionalWheather();

        $form->handleRequest($request);

        // envoie du formulaire
        if($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $data = $this->weatherService->getWeather($formData);
            $dataPrevisionnel =  $this->weatherService->getPrevisionalWheather($formData);
        }

        if(array_key_exists("status", $data) &&  $data["status"] == "errors") {
            return $this->render('weather/exception.html.twig',
            array("data"=>$data            ));
        }
        else if(array_key_exists("status", $dataPrevisionnel) &&  $dataPrevisionnel["status"] == "errors") {
            return $this->render('weather/exception.html.twig',
            array("data"=>$dataPrevisionnel            ));
        }
        else {
            return $this->render('weather/index.html.twig',
            array("data"=>$data,
                "dataPrevisionnel" => $dataPrevisionnel,
                "form" => $form->createView()
            ));
        }
    }
}
