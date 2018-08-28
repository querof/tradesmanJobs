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
use MyHammer\JobsBundle\Entity\Service;
use MyHammer\JobsBundle\Entity\Job;
use MyHammer\JobsBundle\Entity\User;
use MyHammer\JobsBundle\Tools\Tools;

class JobsController extends FOSRestController
{
    public function getAction(Request $request)
    {
        if($request->get('zipcode')!==null && $request->get('zipcode')!=='' && Tools::zipValidation($request->get('zipcode'),"DE")===false) return new View("zipcode: ".$request->get('zipcode')." it's not a German valid zipcode" , Response::HTTP_NOT_ACCEPTABLE);

        $em = $this->getDoctrine()->getManager();

        $qb = $em->createQueryBuilder();

        $qb->select('j')->from('MyHammerJobsBundle:Job', 'j')
              ->leftJoin('j.city', 'c')
              ->leftJoin('c.country', 'ct')
              ->where('ct.isocode = :isocode')
              ->andWhere('j.user <> :user')
              ->andWhere('DATE_DIFF(CURRENT_DATE(),j.date) <= 30')
              ->setParameter('isocode', 'DE')
              ->setParameter('user', $request->get('user'));


        if($request->get('zipcode')!==null && $request->get('zipcode')!=='') $qb->andWhere('c.zipcode = :zipcode')->setParameter('zipcode', $request->get('zipcode'));

        if($request->get('service')!==null && $request->get('service')!=='') $qb->andWhere('j.service = :service')->setParameter('service', $request->get('service'));

        $result = $qb->getQuery()->getResult();

        if ($result === null)  return new View("No jobs are registrered", Response::HTTP_NOT_FOUND);

        return $result;
    }

    public function idAction($id)
    {
      $result = $this->getDoctrine()->getRepository('MyHammerJobsBundle:Job')->find($id);

      if ($result === null) return new View("Job not found", Response::HTTP_NOT_FOUND);

      return $result;
    }

    public function postAction(Request $request)
    {
        return $this->jobObj(0, $request);
    }

    public function updateAction($id, Request $request)
    {
      return $this->jobObj($id, $request);
    }

    private function jobObj($id,Request $request)
    {
      $jobsParameters = $this->jobObjValidate($id,$request);

      if ($jobsParameters['errors']) return new View( $jobsParameters['errors'], Response::HTTP_NOT_ACCEPTABLE);

      $em = $this->getDoctrine()->getManager();
      $em->persist($jobsParameters['data']);
      $em->flush();

      return new View($id==0?"Job Added Successfully":"Job Updated Successfully", ($id==0?201:Response::HTTP_OK));
    }

    private function jobObjValidate($id,Request $request)
    {
      $validator = $this->get('validator');

      $jobsParameters = $this->jobParameters($id,$request);

      $data = $this->jobObjCreate($jobsParameters);

      $errors = $validator->validate($jobsParameters['data']);

      if (count($errors) > 0) $jobsParameters['errors']= $this->replaceFields((string) $errors);

      return $jobsParameters;
    }

    private function jobObjCreate($objData)
    {
      $data = $objData['data'];

      $data->setCity($objData['city']);
      $data->setService($objData['service']);
      $data->setTitle($objData['title']);
      $data->setDescription($objData['description']);
      $data->setDate( \DateTime::createFromFormat('Y/m/d',$objData['date']));
      $data->setUser($objData['user']);

      return $data;
    }

    private function jobParameters($id,Request $request)
    {
      if($id==0)
      {
        $data = new Job;
      }
      else
      {
        $data = $this->getDoctrine()->getRepository('MyHammerJobsBundle:Job')->find($id);
      }
      $city = $this->getDoctrine()->getRepository('MyHammerJobsBundle:City')->find($request->get('city'));

      $service =  $this->getDoctrine()->getRepository('MyHammerJobsBundle:Service')->find($request->get('service'));

      $user =  $this->getDoctrine()->getRepository('MyHammerJobsBundle:User')->find($request->get('user'));

      return array('data' => $data, 'city' => $city,'service' => $service, 'title' => $request->get('title'), 'description' => $request->get('description'), 'date' => $request->get('date'), 'user' => $user,'errors' => null);
    }

    private function replaceFields($string)
    {
      $fieldInfo = array("Object(MyHammer\\JobsBundle\\Entity\\Job).", "\n", " (code d94b19cc-114f-4f44-9cc4-4138e80a87b9)","(code 9ff3fdc4-b214-49db-8718-39c315e33d45)");

      return str_replace($fieldInfo, "", $string);
    }
}
