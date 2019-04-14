<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Glossary;
use AppBundle\Form\GlossaryType;

/**
 * Glossary controller.
 *
 * @Route("/glossary")
 */
class GlossaryController extends Controller
{

    /**
     * Lists all Glossary entities.
     *
     * @Route("/", name="admin_glossary")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AppBundle:Glossary')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Glossary entity.
     *
     * @Route("/", name="admin_glossary_create")
     * @Method("POST")
     * @Template("AppBundle:Glossary:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Glossary();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_glossary_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Glossary entity.
     *
     * @param Glossary $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Glossary $entity)
    {
        $form = $this->createForm(new GlossaryType(), $entity, array(
            'action' => $this->generateUrl('admin_glossary_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Glossary entity.
     *
     * @Route("/new", name="admin_glossary_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Glossary();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Glossary entity.
     *
     * @Route("/{id}", name="admin_glossary_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Glossary')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Glossary entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Glossary entity.
     *
     * @Route("/{id}/edit", name="admin_glossary_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Glossary')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Glossary entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Glossary entity.
    *
    * @param Glossary $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Glossary $entity)
    {
        $form = $this->createForm(new GlossaryType(), $entity, array(
            'action' => $this->generateUrl('admin_glossary_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Glossary entity.
     *
     * @Route("/{id}", name="admin_glossary_update")
     * @Method("PUT")
     * @Template("AppBundle:Glossary:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Glossary')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Glossary entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('admin_glossary_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Glossary entity.
     *
     * @Route("/{id}", name="admin_glossary_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppBundle:Glossary')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Glossary entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_glossary'));
    }

    /**
     * Creates a form to delete a Glossary entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_glossary_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
