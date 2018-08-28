<?php

namespace MyHammer\JobsBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\View\View;

use MyHammer\JobsBundle\Entity\City;
use MyHammer\JobsBundle\Tools\Tools;


class CityController extends FOSRestController
{
    public function getCityAction(Request $request)
    {
        if(empty($request->get('zipcode'))) return $this->getDoctrine()->getRepository('MyHammerJobsBundle:City')->findAll();

        if(Tools::zipValidation($request->get('zipcode'),"DE")===false) return new View("zipcode: ".$request->get('zipcode')." it's not a German valid zipcode" , Response::HTTP_NOT_ACCEPTABLE);

        $city = $this->getDoctrine()->getRepository('MyHammerJobsBundle:City')->findBy(array('zipcode'=>$request->get('zipcode')));

        if(empty($city)) return new View("Can't find a city record with zipcode: ".$request->get('zipcode'), Response::HTTP_NOT_FOUND);

        return $city;
    }

}
