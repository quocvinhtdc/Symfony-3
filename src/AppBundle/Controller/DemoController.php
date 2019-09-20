<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Todo;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class DemoController extends Controller
{
    /**
     * @Route("/todo", name="todo_list")
     */
    public function listAction()
    {
       $todo = $this->getDoctrine()->getRepository("AppBundle:Todo")
           ->findAll();

       return $this->render("Home/list.html.twig", [
          'todo' => $todo,
       ]);
    }

    /**
     * @Route("/todo/create", name="todo_create")
     */
    public function CreateAction(Request $request)
    {
        $todo = new Todo();
        $form = $this->createFormBuilder($todo)
            ->add('name', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'mb-2' )))
            ->add('category', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'mb-2' )))
            ->add('description', TextareaType::class, array('attr' => array('class' => 'form-control', 'style' => 'mb-2' )))
            ->add('priority', ChoiceType::class, array( 'choices' => array('Low' => 'Low', 'Normal' => 'Normal', 'Hard' => 'Hard'), 'attr' => array('class' => 'form-control', 'style' => 'mb-2' )))
            ->add('due_date', DateTimeType::class, array('attr' => array('class' => 'mb-3', 'style' => 'mb-3' )))
            ->add('save', SubmitType::class, array('label' => 'Create Todo','attr' => array('class' => 'btn btn-primary', 'style' => 'mt-2' )))
            ->getForm();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            // get data from Form
            $name = $form['name']->getData();
            $category = $form['category']->getData();
            $description = $form['description']->getData();
            $priority = $form['priority']->getData();
            $due_date = $form['due_date']->getData();
            $time_now = new\DateTime('now');

            // set data
            $todo->setName($name);
            $todo->setCategory($category);
            $todo->setDescription($description);
            $todo->setPriority($priority);
            $todo->setDueDate($due_date);
            $todo->setCreateDate($time_now);

            // add to db
            $em = $this->getDoctrine()->getManager();
            $em->persist($todo);
            $em->flush();

            // them thong bao
            $this->addFlash(
                'notice', 'Add Success');

            // chuyen ve trang list
            return $this->redirectToRoute('todo_list');
        }
        return $this->render('Home/create.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/todo/detail/{id}", name="todo_detail")
     */
    public function DetailAction($id)
    {
        $todo = $this->getDoctrine()->getRepository('AppBundle:Todo')
        ->find($id);
        return $this->render('Home/details.html.twig', array(
            'todo' => $todo,
        ));
    }

    /**
     * @Route("/todo/edit/{id}", name="todo_edit")
     */
    public function editAction(Request $request, $id)
    {
        $todo_edit = $this->getDoctrine()->getRepository('AppBundle:Todo')
            ->find($id);

        $todo_edit->setName($todo_edit->getName());
        $todo_edit->setCategory($todo_edit->getCategory());
        $todo_edit->setPriority($todo_edit->getPriority());
        $todo_edit->setdueDate($todo_edit->getDueDate());
        $now = new\DateTime('now');
        $todo_edit->setCreateDate($now);


        $form_edit = $this->createFormBuilder($todo_edit)
        ->add('name', TextType::class, array('attr' => array('class' => 'form-control')))
        ->add('category', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('priority', ChoiceType::class, array('choices' => array('Low' => 'Low', 'Normal' => 'Normal', 'Hard' => 'Hard') , 'attr' => array('class' => 'form-control')))
            ->add('dueDate', DateTimeType::class, array('attr' => array('class' => '')))
        ->add('save', SubmitType::class, array('attr' => array('class' => 'btn btn-primary mt-3')))
            ->getForm();

        $form_edit->handleRequest($request);
        if($form_edit->isSubmitted() && $form_edit->isValid()) {
            // get data from Form
            $name = $form_edit['name']->getData();
            $category = $form_edit['category']->getData();
            $priority = $form_edit['priority']->getData();
            $due_date = $form_edit['dueDate']->getData();
            $time_now = new\DateTime('now');

            // set data
            $todo_edit->setName($name);
            $todo_edit->setCategory($category);
            $todo_edit->setPriority($priority);
            $todo_edit->setDueDate($due_date);
            $todo_edit->setCreateDate($time_now);

            $em = $this->getDoctrine()->getManager();
            $em->persist($todo_edit);
            $em->flush();

            // thong bao
            $this->addFlash(
                'notice',
                'Edit Success'
            );

        }
        return $this->render('Home/edit.html.twig', array(
            'form_edit' => $form_edit->createView(),
            'todo'   => $todo_edit,
        ));
    }

    /**
     * @Route("/todo/delete/{id}", name="todo_delete")
     */
    public function DeleteAction($id)
    {

        $del_todo = $this->getDoctrine()->getRepository('AppBundle:Todo')
            ->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($del_todo);
        $em->flush();

        // thong bao
        $this->addFlash('notice', 'Delete Success');

        return $this->redirectToRoute('todo_list');
    }
}
