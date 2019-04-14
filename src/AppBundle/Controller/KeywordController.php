<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Keyword;
use AppBundle\Form\KeywordType;

/**
 * Keyword controller.
 *
 * @Route("/keyword")
 */
class KeywordController extends Controller
{

    /**
     * Lists all Keyword entities.
     *
     * @Route("/", name="keyword")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AppBundle:Keyword')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Keyword entity.
     *
     * @Route("/", name="admin_keyword_create")
     * @Method("POST")
     * @Template("AppBundle:Keyword:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Keyword();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_keyword_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Keyword entity.
     *
     * @param Keyword $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Keyword $entity)
    {
        $form = $this->createForm(new KeywordType(), $entity, array(
            'action' => $this->generateUrl('admin_keyword_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Keyword entity.
     *
     * @Route("/new", name="admin_keyword_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Keyword();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Keyword entity.
     *
     * @Route("/{id}", name="admin_keyword_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Keyword')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Keyword entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Keyword entity.
     *
     * @Route("/{id}/edit", name="admin_keyword_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Keyword')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Keyword entity.');
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
    * Creates a form to edit a Keyword entity.
    *
    * @param Keyword $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Keyword $entity)
    {
        $form = $this->createForm(new KeywordType(), $entity, array(
            'action' => $this->generateUrl('admin_keyword_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Keyword entity.
     *
     * @Route("/{id}", name="admin_keyword_update")
     * @Method("PUT")
     * @Template("AppBundle:Keyword:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Keyword')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Keyword entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('admin_keyword_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Keyword entity.
     *
     * @Route("/{id}", name="admin_keyword_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppBundle:Keyword')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Keyword entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('keyword'));
    }

    /**
     * Creates a form to delete a Keyword entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_keyword_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
