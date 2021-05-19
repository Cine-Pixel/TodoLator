<?php

namespace App\Controller;

use App\Entity\Note;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NoteController extends AbstractController
{
    #[Route('/detail/{id}/note/remove/{nid}', name: 'note-remove')]
    public function removeNote($id, $nid) {
        $em = $this->getDoctrine()->getManager();
        $note = $em->getRepository(Note::class)->find($nid);

        if(!$note) 
            throw $this->createNotFoundException('No task found with id'.$nid);
        
        $em->remove($note);
        $em->flush();

        $this->addFlash(type: 'success', message: 'Note was removed');

        $url = $this->generateUrl(route: 'detail', parameters:['id' => $id]);

        return $this->redirect($url);
    }
}
