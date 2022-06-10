<?php

namespace App\Infrastructure\Controller\Post;

use App\Application\Post\CreatePost\CreatePostCommand;
use App\Application\Post\CreatePost\CreatePostUseCase;
use App\Domain\Post\Exceptions\InvalidPostDataException;

use App\Infrastructure\Persistence\Doctrine\Post\Post;

use App\UI\Form\Post\PostType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/posts/create", name="app.post.create")
 */
class CreatePostController extends AbstractController
{
    /**
     * @param Request           $request
     * @param CreatePostUseCase $createPostUseCase
     *
     * @return Response
     * @throws InvalidPostDataException
     */
    public function __invoke(Request $request, CreatePostUseCase $createPostUseCase): Response
    {
        $post = new Post();

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            $createPostCommand = new CreatePostCommand(
                $post->getTitle(),
                $post->getContent(),
                $post->getPublishedAt()
            );

            $post = $createPostUseCase->execute($createPostCommand);

            $this->addFlash('success', "{$post->getTitle()} created.");
            return $this->redirectToRoute('app.post.create');
        }

        return $this->render('post/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
