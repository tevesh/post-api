<?php

    declare(strict_types=1);

    namespace App\Controller;


    use App\Entity\BlogPost;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Component\HttpFoundation\Request;

    /**
     * Class BlogController
     * @Route("/blog")
     *
     * @package App\Controller
     */
    class BlogController extends AbstractController
    {
        /**
         * @Route("/{page}", name="blog_list", methods={"GET"}, defaults={"page":1}, requirements={"page":"\d+"})
         *
         * @param int $page
         *
         * @param Request $request
         *
         * @return JsonResponse
         */
        public function list(Request $request, int $page = 1): JsonResponse
        {
            $limit = (int) $request->get('limit', 15);
            $blogPostRepository = $this->getDoctrine()->getRepository('App:BlogPost');
            $blogPosts = $blogPostRepository->findAllPaginated($page, $limit, BlogPost::ORDER_DESCENDANT);
            $result = [
                'page'  => $page,
                'limit' => $limit,
                'data'  => array_map(function (BlogPost $item) {
                    return $this->generateUrl('blog_by_slug', ['slug' => $item->getSlug()]);
                }, $blogPosts),
            ];

            return $this->json($result, Response::HTTP_OK);
        }

        /**
         * @Route("/post/{id}", name="blog_by_id", requirements={"id":"\d+"}, methods={"GET"})
         * @ParamConverter("blogPost", class="App:BlogPost")
         *
         * Symfony automatically find entities by the route annotation {id} and perform a find($id)
         *
         * @param BlogPost $blogPost
         *
         * @return JsonResponse
         */
        public function post(BlogPost $blogPost): JsonResponse
        {
            // Return the entity with proper format and status code
            return $this->json($blogPost, Response::HTTP_OK);
        }

        /**
         * @Route("/post/{slug}", name="blog_by_slug", requirements={"slug":"[\w-]+"}, methods={"GET"})
         * @ParamConverter("blogPost", class="App:BlogPost", options={"mapping": {"slug": "slug"}})
         *
         * Symfony automatically find entities by the route annotation {slug} and perform a findOneBy(['slug' => $slug])
         *
         * @param BlogPost $blogPost
         *
         * @return JsonResponse
         */
        public function postBySlug(BlogPost $blogPost): JsonResponse
        {
            // Return the entity with proper format and status code
            return $this->json($blogPost, Response::HTTP_OK);
        }

        /**
         * @Route("/add", name="blog_add", methods={"POST"})
         *
         * @param Request $request
         *
         * @return JsonResponse
         */
        public function add(Request $request): JsonResponse
        {
            // Get serializer service
            $serializer = $this->get('serializer');
            // Create an entity from request content
            $blogPost = $serializer->deserialize($request->getContent(), BlogPost::class, 'json');
            // Get the doctrine entity manager
            $em = $this->getDoctrine()->getManager();
            // Save the new entity inside database
            $em->persist($blogPost);
            // Cleanup ALL the operations of the entity manager
            $em->flush();

            // Return the response format as json
            return $this->json($blogPost, Response::HTTP_OK);
        }

        /**
         * @Route("/delete/{id}", name="blog_delete", requirements={"id":"\d+"}, methods={"DELETE"})
         *
         * @param BlogPost $blogPost
         *
         * @return JsonResponse
         */
        public function delete(BlogPost $blogPost): JsonResponse
        {
            // Get the doctrine entity manager
            $em = $this->getDoctrine()->getManager();
            // Remove the entity inside database
            $em->remove($blogPost);
            // Cleanup ALL the operations of the entity manager
            $em->flush();

            // Return the response format as json
            return $this->json(null, Response::HTTP_NO_CONTENT);
        }

    }