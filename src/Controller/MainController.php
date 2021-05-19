<?php

namespace App\Controller;

use App\Entity\Note;
use App\Entity\Todo;
use App\Form\TodoType;
use App\Form\NoteType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class MainController extends AbstractController {
    #[Route('/', name: 'home')]
    public function index() {
        $todos = $this->getDoctrine()->getRepository(Todo::class)->findAll();

        return $this->render(view: 'home/index.html.twig', parameters: ['todos' => $todos]);
    }

    #[Route('/detail/{id}', name: 'detail')]
    public function todoDetail(Request $request, $id) {
        $todo = $this->getDoctrine()->getRepository(Todo::class)->find($id);
        $note = new Note();
        $noteForm = $this->createForm(type: NoteType::class, data: $note);
        $noteForm->handleRequest($request);

        if($noteForm->isSubmitted()) {
            $todo = $todo->addNote($note);
            $em = $this->getDoctrine()->getManager();
            $em->persist($note);
            $em->persist($todo);
            $em->flush();

            $url = $this->generateUrl(route: 'detail', parameters: ['id' => $id]);

            return $this->redirect($url);
        }

        return $this->render(view: 'home/todoDetail.html.twig', parameters: [
                'todo' => $todo, 
                'notes' => $todo->getNotes(),
                'noteForm' => $noteForm->createView()
            ]
        );
    }

    #[Route('/create', name: 'create')]
    public function create(Request $request) {
        $todo = new Todo();
        $form = $this->createForm(type: TodoType::class, data: $todo);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($todo);
            $em->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render(view: 'home/todoCreate.html.twig', parameters: ['form' => $form->createView()]);
    }

    #[Route('/update/{id}', name: 'update')]
    public function update(Request $request, $id) {
        $todo = new Todo();
        $todo = $this->getDoctrine()->getRepository(Todo::class)->find($id);

        $form = $this->createFormBuilder($todo)
            ->add('title', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('description', TextType::class, array(
                'required' => false,
                'attr' => array('class' => 'form-control')
            ))
            ->add('save', SubmitType::class, array(
                'label' => 'Update',
                'attr' => array('class' => 'btn btn-primary mt-3')
            ))
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('home/update.html.twig', array(
            'form' => $form->createView()
        ));
    }

    #[Route('/remove/{id}', name: 'remove')]
    public function remove($id) {
        $em = $this->getDoctrine()->getManager();
        $todo = $em->getRepository(Todo::class)->find($id);

        if(!$todo) 
            throw $this->createNotFoundException('No task found with id'.$id);
        
        $em->remove($todo);
        $em->flush();

        $this->addFlash(type: 'success', message: 'Todo was removed');

        return $this->redirectToRoute('home');
    }
    
    #[Route('/custom/{name?}', name: 'custom')]
    public function custom(string $name) {
        return $this->render(view: 'home/custom.html.twig', parameters: ['name' => $name]);
    }
}
