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


/*
 * Controller thats containts the main actions of the API, in order to get and
 * send jobs data to the backend.
 */

class JobsController extends FOSRestController
{

    /**
     * Returns a doctrine object and FOSRestBundle will turn it in a
     * json object. Only return jobs for user thats are not the creator of the
     * record, country isocode = $this->getParameter('isocode')
     * (set in parameters as 'DE', Deutschland isocode) and the date has 30 days
     * or less of been created.
     *
     * @param Request object $request. Can containt zipcode and service, to
     * filter the query; but they are optional.
     *
     * @return Json of MyHammer\JobsBundle\Entity\Job class instace; according with resulset of the database.
     */

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
                  ->setParameter('isocode', $this->getParameter('isocode'))
                  ->setParameter('user', $request->get('user'));


        if($request->get('zipcode')!==null && $request->get('zipcode')!=='') $qb->andWhere('c.zipcode = :zipcode')->setParameter('zipcode', $request->get('zipcode'));

        if($request->get('service')!==null && $request->get('service')!=='') $qb->andWhere('j.service = :service')->setParameter('service', $request->get('service'));

        $result = $qb->getQuery()->getResult();

        if ($result === null)  return new View("No jobs are registrered", Response::HTTP_NOT_FOUND);

        return $result;
    }


    /**
     * Returns a resonse an doctrine object and FOSRestBundle will turn it in a json object.
     *
     * @param Integer $id. Id (PK) of MyHammer\JobsBundle\Entity\Job
     *
     * @return Json of MyHammer\JobsBundle\Entity\Job class instace; according with resulset of the *         database.
     */

    public function idAction($id)
    {
        $result = $this->getDoctrine()->getRepository('MyHammerJobsBundle:Job')->find($id);

        if ($result === null) return new View("Job not found", Response::HTTP_NOT_FOUND);

        return $result;
    }


    /**
     * Send a information to create a job in the database.
     *
     * @param Request object $request. Fiels: "city","service","title",
     *                                        "description","date","user"
     *
     * @return mesage of confirmation/error with httpcode.
     */

    public function postAction(Request $request)
    {
        return $this->jobObj(0, $request);
    }


    /**
     * Send a information to update a job in the database. For now update
     * every field, We are waiting for the defintion of the el user history.
     *
     * @param Integer $id. Id (PK) of MyHammer\JobsBundle\Entity\Job.
     * @param Request object $request. Fiels: "city","service","title",
     *                                        "description","date","user"
     *
     * @return mesage of confirmation/error with httpcode.
     */

    public function updateAction($id, Request $request)
    {
      return $this->jobObj($id, $request);
    }


    /**
     * Ejecute the commit in the database of the insert/update job transactions.
     *
     * @param Integer $id. Id (PK) of MyHammer\JobsBundle\Entity\Job, it can have
     * a valid pk value or "0" (wich means its a create action)
     * @param Request object $request. Fiels: "city","service","title",
     *                                        "description","date","user"
     *
     * @return mesage of confirmation/error with httpcode.
     */

    private function jobObj($id,Request $request)
    {
      $jobsParameters = $this->jobObjValidate($id,$request);

      if ($jobsParameters['errors']) return new View( $jobsParameters['errors'], Response::HTTP_NOT_ACCEPTABLE);

      $em = $this->getDoctrine()->getManager();
      $em->persist($jobsParameters['data']);
      $em->flush();

      return new View($id==0?"Job Added Successfully":"Job Updated Successfully", ($id==0?201:Response::HTTP_OK));
    }


    /**
     * Ejecute the validations in the database of the insert/update job transactions.
     *
     * @param Integer $id. Id (PK) of MyHammer\JobsBundle\Entity\Job, it can have
     * a valid pk value or "0" (wich means its a create action)
     * @param Request object $request. Fiels: "city","service","title",
     *                                        "description","date","user"
     *
     * @return An instance of MyHammer\JobsBundle\Entity\Job, if some exception
     *         occurs an error menssage will be asigned to a
     *         $jobsParameters['errors'] position.
     */

    private function jobObjValidate($id,Request $request)
    {
      $validator = $this->get('validator');

      $jobsParameters = $this->jobParameters($id,$request);

      $data = $this->jobObjCreate($jobsParameters);

      $errors = $validator->validate($jobsParameters['data']);

      if (count($errors) > 0) $jobsParameters['errors']= Tools::replaceFields((string) $errors);

      return $jobsParameters;
    }

    /**
     * Asign values to instance of MyHammer\JobsBundle\Entity\Job.
     *
     * @param Array $objData. Fiels: "city","service","title",
     *                                        "description","date","user"
     *
     * @return An instance of MyHammer\JobsBundle\Entity\Job
     */

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

    /**
     * Find/create MyHammer\JobsBundle\Entity\Job. Create an array with the
     * values and object thats will be used in the insert/update transactions;
     * finding and asigning objects instances of:
     * MyHammer\JobsBundle\Entity\City,MyHammer\JobsBundle\Entity\Service and
     * MyHammer\JobsBundle\Entity\User. Also create a position for errors.
     *
     * @param Integer $id. Id (PK) of MyHammer\JobsBundle\Entity\Job, it must
     * have a valid pk value or "0" (wich means its a create action)
     * @param Request object $request. Fiels: "city","service","title",
     *                                        "description","date","user"
     *
     * @return An instance of MyHammer\JobsBundle\Entity\Job, if some exception
     *         occurs an error menssage will be asigned to a
     *         $jobsParameters['errors'] position.
     */

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
