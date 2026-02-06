<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/comments')]
class CommentController extends AbstractController
{
    #[Route('/admin', name: 'app_comment_admin_index', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function adminIndex(CommentRepository $commentRepository): Response
    {
        return $this->render('comment/admin_index.html.twig', [
            'comments' => $commentRepository->findPendingComments(),
        ]);
    }

    #[Route('/post/{id}/new', name: 'app_comment_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        $comment = new Comment();
        $comment->setPost($post);
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setAuthor($this->getUser());
            $comment->setStatus('pending');
            $entityManager->persist($comment);
            $entityManager->flush();

            $this->addFlash('success', 'Commentaire créé avec succès ! Il sera publié après approbation de l\'admin.');
            return $this->redirectToRoute('app_post_show', ['id' => $post->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('comment/new.html.twig', [
            'comment' => $comment,
            'form' => $form,
            'post' => $post,
        ]);
    }

    #[Route('/{id}/approve', name: 'app_comment_approve', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function approve(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('approve'.$comment->getId(), $request->request->get('_token'))) {
            $comment->setStatus('approved');
            $entityManager->flush();
            
            $this->addFlash('success', 'Commentaire approuvé !');
        }

        return $this->redirectToRoute('app_comment_admin_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'app_comment_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        $postId = $comment->getPost()->getId();
        
        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $entityManager->remove($comment);
            $entityManager->flush();
            
            $this->addFlash('success', 'Commentaire supprimé !');
        }

        return $this->redirectToRoute('app_post_show', ['id' => $postId], Response::HTTP_SEE_OTHER);
    }
}
