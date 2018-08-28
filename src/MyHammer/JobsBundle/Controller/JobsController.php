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

class JobsController extends FOSRestController
{
    public function getAction()
    {
        $result =  $this->getDoctrine()->getRepository('MyHammerJobsBundle:Job')->findAll();

        if ($result === null)  return new View("there are no jobs exist", Response::HTTP_NOT_FOUND);

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

      if (count($errors) > 0) $jobsParameters['errors']= (string) $errors;

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
}
