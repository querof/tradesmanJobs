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


/*
 * Controller thats containts the main actions of the API, in order to get
 * City data to the backend.
 */

class CityController extends FOSRestController
{

  /**
   * Returns a doctrine object and FOSRestBundle will turn it in a
   * json object. Only return  city where
   * isocode = $this->getParameter('isocode') (set in parameters as 'DE',
   * Deutschland isocode)
   *
   * @param Request object $request. Can containt zipcode  to
   * filter the query; but it is optional.
   *
   * @return Json of MyHammer\JobsBundle\Entity\Ciy class instace; according with resulset of the database.
   */

    public function getCityAction(Request $request)
    {

        if($request->get('zipcode')!==null && $request->get('zipcode')!=='' && Tools::zipValidation($request->get('zipcode'),"DE")===false) return new View("zipcode: ".$request->get('zipcode')." it's not a German valid zipcode" , Response::HTTP_NOT_ACCEPTABLE);

        $em = $this->getDoctrine()->getManager();

        $qb = $em->createQueryBuilder();

        $qb->select('c')->from('MyHammerJobsBundle:City', 'c')
              ->leftJoin('c.country', 'ct')
              ->where('ct.isocode = :isocode')
              ->setParameter('isocode', $this->getParameter('isocode'));


        if($request->get('zipcode')!==null && $request->get('zipcode')!=='') $qb->andWhere('c.zipcode = :zipcode')->setParameter('zipcode', $request->get('zipcode'));

        $result = $qb->getQuery()->getResult();

        if ($result === null)  return new View("No city are registrered", Response::HTTP_NOT_FOUND);

        return $result;
    }

}
