<?php

namespace App\Controller;

use App\Service\GorestApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class UserController extends AbstractController
{
    public function index(Request $request, GorestApiService $api, SessionInterface $session)
    {
        $search = $request->query->get('q', '');
        $users = $api->getUsers();

        // lokalne zmiany (edytowani użytkownicy w sesji)
        $edits = $session->get('local_user_edits', []);

        // nadpisz dane z API danymi lokalnymi
        $users = array_map(fn($user) => $edits[$user['id']] ?? $user, $users);

        if ($search) {
            $users = array_filter($users, fn($u) =>
                stripos($u['name'], $search) !== false || stripos($u['email'], $search) !== false
            );
        }

        return $this->render('users/index.html.twig', [
            'users' => $users,
            'search' => $search,
        ]);
    }

    public function edit(Request $request, int $id, SessionInterface $session, GorestApiService $api)
    {
        if ($request->isXmlHttpRequest()) {
            $data = json_decode($request->getContent(), true);
            $data['id'] = $id;

            $edits = $session->get('local_user_edits', []);
            $edits[$id] = $data;
            $session->set('local_user_edits', $edits);

             $data = json_decode($request->getContent(), true);
    $data['id'] = $id;

    $edits = $session->get('local_user_edits', []);
    $edits[$id] = $data;
    $session->set('local_user_edits', $edits);

    // Zwróć informację, że można przekierować
    return new JsonResponse(['success' => true, 'redirect' => $this->generateUrl('user_list')]);

        }

        $user = null;
        $edits = $session->get('local_user_edits', []);
        if (isset($edits[$id])) {
            $user = $edits[$id];
        } else {
            foreach ($api->getUsers() as $u) {
                if ($u['id'] == $id) {
                    $user = $u;
                    break;
                }
            }
        }

        if (!$user) {
            throw $this->createNotFoundException('User not found.');
        }

        return $this->render('users/edit.html.twig', ['user' => $user]);
    }
}
