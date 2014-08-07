<?php

namespace DocDigital\Bundle\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\View\TwitterBootstrap3View;

use DocDigital\Bundle\UserBundle\Entity\Role;
use DocDigital\Bundle\UserBundle\Form\RoleType;

/**
 * Role controller.
 *
 * @Route("/admin/role")
 */
class RoleController extends Controller
{

    /**
     * Lists all Role entities.
     *
     * @Route("/", name="admin_role")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function indexAction()
    {
        list($filterForm, $queryBuilder) = $this->filter();

        list($entities, $pagerHtml) = $this->paginator($queryBuilder);

        return array(
            'entities'   => $entities,
            'pagerHtml'  => $pagerHtml,
            'filterForm' => $filterForm->createView(),
        );
    }

    /**
     * Create filter form and process filter request.
     */
    protected function filter()
    {
        $request = $this->getRequest();
        $session = $request->getSession();
        $filterForm = $this->createFilterForm(new RoleType());
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('DdUserBundle:Role')->createQueryBuilder('e');

        // Reset filter
        if ($request->get('filter_action') == 'reset') {
            $session->remove('RoleControllerFilter');
        }

        // Filter action
        if ($request->get('filter_action') == 'filter') {
            // Bind values from the request
            $filterForm->handleRequest($request);

            if ($filterForm->isValid()) {
                // Build the query from the given form object
                $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($filterForm, $queryBuilder);
                // Save filter to session
                $filterData = $request->get($filterForm->getName());
                $session->set('RoleControllerFilter', $filterData);
            }
        } else {
            // Get filter from session
            if ($session->has('RoleControllerFilter')) {
                $filterData = $session->get('RoleControllerFilter');
                $filterForm->submit($filterData);
                $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($filterForm, $queryBuilder);
            }
        }

        return array($filterForm, $queryBuilder);
    }

    /**
     * Get results from paginator and get paginator view.
     */
    protected function paginator($queryBuilder)
    {
        // Paginator
        $adapter = new DoctrineORMAdapter($queryBuilder);
        $pagerfanta = new Pagerfanta($adapter);
        $currentPage = $this->getRequest()->get('page', 1);
        $pagerfanta->setCurrentPage($currentPage);
        $entities = $pagerfanta->getCurrentPageResults();

        // Paginator - route generator
        $me = $this;
        $routeGenerator = function($page) use ($me)
        {
            return $me->generateUrl('admin_role', array('page' => $page));
        };

        // Paginator - view
        $translator = $this->get('translator');
        $view = new TwitterBootstrap3View();
        $pagerHtml = $view->render(
            $pagerfanta,
            $routeGenerator,
            array(
                'proximity' => 3,
                'prev_message' => $translator->trans('views.index.pagprev'),
                'next_message' => $translator->trans('views.index.pagnext'),
            )
        );

        return array($entities, $pagerHtml);
    }

    /**
     * Creates a Filter form to search for Entities.
     *
     * @param AbstractType|string $formType The `generate:doctrine:form` generated Type or its FQCN.
     *
     * @return \Symfony\Component\Form\Form The filter Form
     */
    private function createFilterForm($formType)
    {
        $adapter = $this->get('dd_form.form_adapter');
        $form = $adapter->adaptForm(
            $formType,
            $this->generateUrl('admin_role')
        );
        $form->remove('submit');
        return $form;
    }
    /**
     * Creates a new Role entity.
     *
     * @Route("/create", name="admin_role_create")
     * @Method("POST")
     * @Template("DdUserBundle:Role:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Role();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success',
                $this->get('translator')->trans('flash.create.success')
            );

            return $this->redirect($this->generateUrl('admin_role_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a Role entity.
    *
    * @param Role $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Role $entity)
    {
        $form = $this->createForm(new RoleType(), $entity, array(
            'action' => $this->generateUrl('admin_role_create'),
            'method' => 'POST',
        ));

                return $form;
    }

    /**
     * Displays a form to create a new Role entity.
     *
     * @Route("/new", name="admin_role_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Role();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Role entity.
     *
     * @Route("/{id}/show", name="admin_role_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DdUserBundle:Role')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Role entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Role entity.
     *
     * @Route("/{id}/edit", name="admin_role_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DdUserBundle:Role')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Role entity.');
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
    * Creates a form to edit a Role entity.
    *
    * @param Role $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Role $entity)
    {
        $form = $this->createForm(new RoleType(), $entity, array(
            'action' => $this->generateUrl('admin_role_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

                return $form;
    }
    /**
     * Edits an existing Role entity.
     *
     * @Route("/{id}/update", name="admin_role_update")
     * @Method("PUT")
     * @Template("DdUserBundle:Role:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DdUserBundle:Role')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Role entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success',
                $this->get('translator')->trans('flash.update.success')
            );

            return $this->redirect($this->generateUrl('admin_role_edit', array('id' => $id)));
        } else {
            $this->get('session')->getFlashBag()->add(
                'danger',
                $this->get('translator')->trans('flash.update.error')
            );
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Role entity.
     *
     * @Route("/{id}", name="admin_role_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('DdUserBundle:Role')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Role entity.');
            }

            $em->remove($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success',
                $this->get('translator')->trans('flash.delete.success')
            );
        } else {
            $this->get('session')->getFlashBag()->add(
                'danger',
                $this->get('translator')->trans('flash.delete.error')
            );
        }

        return $this->redirect($this->generateUrl('admin_role'));
    }

    /**
     * Creates a form to delete a Role entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_role_delete', array('id' => $id)))
            ->setMethod('DELETE')            ->getForm()
        ;
    }
}
